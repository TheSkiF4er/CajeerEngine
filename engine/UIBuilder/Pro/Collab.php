<?php
namespace UIBuilder\Pro;

use Database\DB;

class Collab
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/frontend_v3_4.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function acquire(string $docType, string $docId, int $userId, int $ttlSec = 30, int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $token = bin2hex(random_bytes(16));
        $now = date('Y-m-d H:i:s');
        $exp = date('Y-m-d H:i:s', time()+max(5,$ttlSec));

        // upsert lock if expired or same user
        $row = $pdo->query("SELECT * FROM ce_builder_locks WHERE tenant_id=".(int)$tenantId." AND doc_type=".$pdo->quote($docType)." AND doc_id=".$pdo->quote($docId)." LIMIT 1")
                   ->fetch(\PDO::FETCH_ASSOC);
        if ($row && strtotime((string)$row['expires_at']) > time() && (int)($row['user_id'] ?? 0) !== $userId) {
            return ['ok'=>false,'error'=>'locked','by'=>(int)($row['user_id'] ?? 0),'expires_at'=>$row['expires_at']];
        }

        $pdo->prepare("INSERT INTO ce_builder_locks(tenant_id,doc_type,doc_id,user_id,lock_token,acquired_at,expires_at)
                       VALUES(:t,:dt,:di,:u,:tk,:a,:e)
                       ON DUPLICATE KEY UPDATE user_id=:u2, lock_token=:tk2, acquired_at=:a2, expires_at=:e2")
            ->execute([
                ':t'=>$tenantId,':dt'=>$docType,':di'=>$docId,':u'=>$userId,':tk'=>$token,':a'=>$now,':e'=>$exp,
                ':u2'=>$userId,':tk2'=>$token,':a2'=>$now,':e2'=>$exp,
            ]);

        return ['ok'=>true,'lock_token'=>$token,'expires_at'=>$exp];
    }

    public static function appendPatch(string $docType, string $docId, int $userId, array $patch, int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $pdo->prepare("INSERT INTO ce_builder_changes(tenant_id,doc_type,doc_id,user_id,patch_json,created_at)
                       VALUES(:t,:dt,:di,:u,:p,NOW())")
            ->execute([
                ':t'=>$tenantId,':dt'=>$docType,':di'=>$docId,':u'=>$userId,
                ':p'=>json_encode($patch, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);

        return ['ok'=>true];
    }
}
