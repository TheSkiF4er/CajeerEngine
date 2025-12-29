<?php
namespace SaaS;

use Database\DB;
use Security\AuditTrail;

class TenantManager
{
    public static function ensureSchema(): void { AuditTrail::ensureSchema(); }

    public static function getBySlug(string $slug): ?array
    {
        $pdo = DB::pdo(); if(!$pdo) return null;
        $st = $pdo->prepare("SELECT * FROM ce_tenants WHERE slug=:s LIMIT 1");
        $st->execute([':s'=>$slug]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public static function setStatus(int $tenantId, string $status): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $status = strtolower(trim($status));
        $allowed = ['active','suspended','archived','deleted'];
        if (!in_array($status, $allowed, true)) return ['ok'=>false,'error'=>'invalid_status'];

        $fields = ['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')];
        if ($status==='suspended') $fields['suspended_at']=date('Y-m-d H:i:s');
        if ($status==='archived') $fields['archived_at']=date('Y-m-d H:i:s');
        if ($status==='deleted')  $fields['deleted_at']=date('Y-m-d H:i:s');

        $set=[]; $params=[':id'=>$tenantId];
        foreach ($fields as $k=>$v){ $set[]="$k=:$k"; $params[":$k"]=$v; }
        $pdo->prepare("UPDATE ce_tenants SET ".implode(',',$set)." WHERE id=:id")->execute($params);

        AuditTrail::append('tenant.status.update', ['status'=>$status], 'tenant:'.$tenantId);
        return ['ok'=>true,'tenant_id'=>$tenantId,'status'=>$status];
    }

    public static function quotas(int $tenantId): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $row = $pdo->query("SELECT quotas,enforced FROM ce_tenant_quotas WHERE tenant_id=".(int)$tenantId." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return ['ok'=>true,'quotas'=>[],'enforced'=>false];
        $q = json_decode((string)$row['quotas'], true);
        return ['ok'=>true,'quotas'=>is_array($q)?$q:[],'enforced'=>((int)$row['enforced']===1)];
    }

    public static function setQuotas(int $tenantId, array $quotas, bool $enforced=true): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $pdo->prepare("INSERT INTO ce_tenant_quotas(tenant_id,quotas,enforced,updated_at)
                       VALUES(:t,:q,:e,NOW())
                       ON DUPLICATE KEY UPDATE quotas=:q2, enforced=:e2, updated_at=NOW()")
            ->execute([
                ':t'=>$tenantId,
                ':q'=>json_encode($quotas, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ':e'=>$enforced?1:0,
                ':q2'=>json_encode($quotas, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ':e2'=>$enforced?1:0
            ]);
        AuditTrail::append('tenant.quotas.update', ['quotas'=>$quotas,'enforced'=>$enforced], 'tenant:'.$tenantId);
        return ['ok'=>true];
    }

    // Quotas enforcement hook (foundation): callers provide current usage map.
    public static function checkUsage(int $tenantId, array $usage): array
    {
        $q = self::quotas($tenantId);
        if (empty($q['enforced'])) return ['ok'=>true,'enforced'=>false,'violations'=>[]];

        $violations = [];
        $limits = (array)($q['quotas'] ?? []);
        foreach ($limits as $k=>$limit) {
            $cur = $usage[$k] ?? 0;
            if (is_numeric($limit) && is_numeric($cur) && (float)$cur > (float)$limit) {
                $violations[] = ['resource'=>$k,'current'=>$cur,'limit'=>$limit];
            }
        }
        return ['ok'=>count($violations)===0,'enforced'=>true,'violations'=>$violations];
    }
}
