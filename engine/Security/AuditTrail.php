<?php
namespace Security;

use Database\DB;
use Observability\Logger;

/**
 * Immutable audit trail (foundation): hash-chained log records.
 */
class AuditTrail
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/enterprise_v2_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function append(string $action, array $payload = [], ?string $target = null): bool
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return false;

        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $siteId   = (int)($_SERVER['CE_SITE_ID'] ?? 0);
        $actorId  = isset($_SERVER['CE_USER_ID']) ? (int)$_SERVER['CE_USER_ID'] : null;
        $ip       = (string)($_SERVER['REMOTE_ADDR'] ?? '');

        $prev = $pdo->query("SELECT entry_hash FROM ce_audit_log_immutable ORDER BY id DESC LIMIT 1")->fetchColumn();
        $prevHash = $prev ? (string)$prev : null;

        $created = date('Y-m-d H:i:s');
        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        $material = implode('|', [
            (string)$tenantId,
            (string)$siteId,
            (string)$actorId,
            $ip,
            $action,
            (string)$target,
            (string)$payloadJson,
            $created,
            (string)$prevHash
        ]);

        $entryHash = hash('sha256', $material);

        $st = $pdo->prepare("INSERT INTO ce_audit_log_immutable
          (tenant_id,site_id,actor_user_id,actor_ip,action,target,payload_json,created_at,prev_hash,entry_hash)
          VALUES(:t,:s,:u,:ip,:a,:tg,:p,:c,:ph,:eh)");
        $ok = $st->execute([
            ':t'=>$tenantId, ':s'=>$siteId, ':u'=>$actorId, ':ip'=>$ip,
            ':a'=>$action, ':tg'=>$target, ':p'=>$payloadJson, ':c'=>$created,
            ':ph'=>$prevHash, ':eh'=>$entryHash
        ]);

        if ($ok) Logger::info('audit.append', ['action'=>$action,'target'=>$target,'hash'=>$entryHash]);
        return (bool)$ok;
    }

    public static function verifyChain(int $limit = 1000): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $rows = $pdo->query("SELECT id,tenant_id,site_id,actor_user_id,actor_ip,action,target,payload_json,created_at,prev_hash,entry_hash
                             FROM ce_audit_log_immutable ORDER BY id ASC LIMIT " . (int)$limit)->fetchAll(\PDO::FETCH_ASSOC);
        $prev = null; $bad = [];
        foreach ($rows as $r) {
            $material = implode('|', [
                (string)$r['tenant_id'], (string)$r['site_id'], (string)$r['actor_user_id'], (string)$r['actor_ip'],
                (string)$r['action'], (string)$r['target'], (string)$r['payload_json'], (string)$r['created_at'], (string)$prev
            ]);
            $h = hash('sha256', $material);
            if (($r['prev_hash'] ?? null) !== $prev) $bad[] = ['id'=>$r['id'],'error'=>'prev_hash_mismatch'];
            if (($r['entry_hash'] ?? '') !== $h) $bad[] = ['id'=>$r['id'],'error'=>'entry_hash_mismatch'];
            $prev = (string)$r['entry_hash'];
        }
        return ['ok'=>count($bad)===0,'checked'=>count($rows),'bad'=>$bad];
    }
}
