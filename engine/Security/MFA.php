<?php
namespace Security;

use Database\DB;

/**
 * MFA foundation:
 * - TOTP secret storage placeholder (should be encrypted via Vault in v3.x)
 * - WebAuthn credential storage placeholder
 */
class MFA
{
    public static function ensureSchema(): void { AuditTrail::ensureSchema(); }

    public static function listFactors(int $userId): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        $st = $pdo->prepare("SELECT id,type,label,enabled,created_at,updated_at FROM ce_mfa_factors WHERE user_id=:u ORDER BY id DESC");
        $st->execute([':u'=>$userId]);
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function registerTotp(int $userId, string $label, string $secretEnc): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $st = $pdo->prepare("INSERT INTO ce_mfa_factors(user_id,type,label,secret_enc,enabled,created_at,updated_at)
                             VALUES(:u,'totp',:l,:s,1,NOW(),NOW())");
        $st->execute([':u'=>$userId,':l'=>$label,':s'=>$secretEnc]);
        AuditTrail::append('mfa.totp.register', ['label'=>$label], 'user:'.$userId);
        return ['ok'=>true,'id'=>(int)$pdo->lastInsertId()];
    }

    public static function registerWebauthn(int $userId, string $label, array $webauthn): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $st = $pdo->prepare("INSERT INTO ce_mfa_factors(user_id,type,label,webauthn_json,enabled,created_at,updated_at)
                             VALUES(:u,'webauthn',:l,:j,1,NOW(),NOW())");
        $st->execute([':u'=>$userId,':l'=>$label,':j'=>json_encode($webauthn, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
        AuditTrail::append('mfa.webauthn.register', ['label'=>$label], 'user:'.$userId);
        return ['ok'=>true,'id'=>(int)$pdo->lastInsertId()];
    }
}
