<?php
namespace Intent;

use Database\DB;

class IntentStore
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/platform_v4_0.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function create(int $tenantId, string $name, string $kind, array $desired): int
    {
        $pdo = DB::pdo(); if(!$pdo) return 0;
        self::ensureSchema();
        $j = json_encode($desired, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $st = $pdo->prepare("INSERT INTO ce_platform_intents(tenant_id,name,kind,desired_json,status,created_at,updated_at)
                             VALUES(:t,:n,:k,:d,'pending',NOW(),NOW())");
        $st->execute([':t'=>$tenantId,':n'=>$name,':k'=>$kind,':d'=>$j]);
        return (int)$pdo->lastInsertId();
    }

    public static function list(int $tenantId=0, string $status='pending', int $limit=50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));
        if ($tenantId > 0) {
            $st = $pdo->prepare("SELECT * FROM ce_platform_intents WHERE tenant_id=:t AND status=:s ORDER BY id DESC LIMIT $limit");
            $st->execute([':t'=>$tenantId,':s'=>$status]);
        } else {
            $st = $pdo->prepare("SELECT * FROM ce_platform_intents WHERE status=:s ORDER BY id DESC LIMIT $limit");
            $st->execute([':s'=>$status]);
        }
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function mark(int $id, string $status, string $error=''): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        self::ensureSchema();
        $pdo->prepare("UPDATE ce_platform_intents SET status=:s,last_error=:e,updated_at=NOW() WHERE id=:id")
            ->execute([':s'=>$status,':e'=>$error,':id'=>$id]);
    }
}
