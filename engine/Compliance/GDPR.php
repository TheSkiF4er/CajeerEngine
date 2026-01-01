<?php
namespace Compliance;

use Database\DB;
use Security\AuditTrail;

/**
 * GDPR tooling (foundation):
 * - export: gathers user profile + accessible content references
 * - erase: anonymizes user record (project-specific)
 */
class GDPR
{
    public static function ensureSchema(): void { AuditTrail::ensureSchema(); }

    public static function queueReport(int $tenantId, int $userId, string $type): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $type = strtolower(trim($type));
        if (!in_array($type, ['access','export','erase'], true)) return ['ok'=>false,'error'=>'invalid_type'];

        $pdo->prepare("INSERT INTO ce_access_reports(tenant_id,user_id,report_type,status,created_at,updated_at)
                       VALUES(:t,:u,:rt,'queued',NOW(),NOW())")
            ->execute([':t'=>$tenantId,':u'=>$userId,':rt'=>$type]);

        $id = (int)$pdo->lastInsertId();
        AuditTrail::append('gdpr.report.queue', ['type'=>$type,'report_id'=>$id], 'user:'.$userId);
        return ['ok'=>true,'report_id'=>$id];
    }

    public static function runReport(int $reportId): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $r = $pdo->query("SELECT * FROM ce_access_reports WHERE id=".(int)$reportId." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return ['ok'=>false,'error'=>'not_found'];

        $pdo->prepare("UPDATE ce_access_reports SET status='running', updated_at=NOW() WHERE id=:id")->execute([':id'=>$reportId]);

        $tenantId = (int)$r['tenant_id'];
        $userId = (int)$r['user_id'];
        $type = (string)$r['report_type'];

        $result = ['type'=>$type,'tenant_id'=>$tenantId,'user_id'=>$userId,'generated_at'=>date('c')];

        if ($type === 'access' || $type === 'export') {
            // Foundation export: only user row + installed packages + sites count etc.
            $user = $pdo->query("SELECT id,username,email,group_id,created_at FROM ce_users WHERE id=".(int)$userId." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            $result['user'] = $user ?: null;
            $sites = $pdo->query("SELECT COUNT(*) c FROM ce_sites WHERE tenant_id=".(int)$tenantId)->fetchColumn();
            $result['sites_count'] = (int)($sites ?: 0);
            $result['note'] = 'Foundation export. Extend in v3.x with full content mapping.';
        }

        if ($type === 'erase') {
            // Foundation erase: anonymize email, keep id
            $pdo->prepare("UPDATE ce_users SET email=NULL, username=CONCAT('anon_',id) WHERE id=:id")->execute([':id'=>$userId]);
            $result['erased'] = true;
            $result['note'] = 'Foundation erase. Extend to content PII fields in v3.x.';
            AuditTrail::append('gdpr.erase', ['user_id'=>$userId], 'user:'.$userId);
        }

        $pdo->prepare("UPDATE ce_access_reports SET status='done', result_json=:r, updated_at=NOW() WHERE id=:id")
            ->execute([':r'=>json_encode($result, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), ':id'=>$reportId]);

        return ['ok'=>true,'report_id'=>$reportId,'result'=>$result];
    }
}
