<?php
namespace Security;

use Database\DB;

/**
 * SSO foundation (OIDC / SAML): configuration storage + minimal validation hooks.
 * Actual protocol flows should be implemented in v3.x with tested libraries.
 */
class SSO
{
    public static function ensureSchema(): void
    {
        AuditTrail::ensureSchema();
    }

    public static function listProviders(int $tenantId): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        $st = $pdo->prepare("SELECT id,type,name,enabled,config_json,updated_at FROM ce_sso_providers WHERE tenant_id=:t ORDER BY id DESC");
        $st->execute([':t'=>$tenantId]);
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function upsertProvider(int $tenantId, string $type, string $name, array $config, bool $enabled = true): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $type = strtolower(trim($type));
        if (!in_array($type, ['oidc','saml'], true)) return ['ok'=>false,'error'=>'invalid_type'];

        $cfg = json_encode($config, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $st = $pdo->prepare("INSERT INTO ce_sso_providers(tenant_id,type,name,enabled,config_json,created_at,updated_at)
                             VALUES(:t,:ty,:n,:e,:c,NOW(),NOW())");
        $st->execute([':t'=>$tenantId,':ty'=>$type,':n'=>$name,':e'=>$enabled?1:0,':c'=>$cfg]);
        AuditTrail::append('sso.provider.create', ['type'=>$type,'name'=>$name], 'tenant:'.$tenantId);
        return ['ok'=>true,'id'=>(int)$pdo->lastInsertId()];
    }
}
