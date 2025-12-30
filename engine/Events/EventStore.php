<?php
namespace Core\Events;

use Database\DB;
use Observability\Logger;

class EventStore
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/async_v3_1.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function store(string $name, array $payload = [], int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $st = $pdo->prepare("INSERT INTO ce_events(tenant_id,name,payload_json,status,created_at) VALUES(:t,:n,:p,'stored',NOW())");
        $st->execute([':t'=>$tenantId,':n'=>$name,':p'=>json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
        $id = (int)$pdo->lastInsertId();
        Logger::info('events.store', ['id'=>$id,'name'=>$name]);
        return ['ok'=>true,'event_id'=>$id];
    }

    public static function markProcessed(int $id): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_events SET status='processed', processed_at=NOW() WHERE id=:id")->execute([':id'=>$id]);
    }

    public static function markFailed(int $id, string $error): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_events SET status='failed', last_error=:e WHERE id=:id")->execute([':e'=>$error,':id'=>$id]);
    }

    public static function fetchById(int $id): ?array
    {
        $pdo = DB::pdo(); if(!$pdo) return null;
        $r = $pdo->query("SELECT * FROM ce_events WHERE id=".(int)$id." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return null;
        $r['payload'] = json_decode((string)($r['payload_json'] ?? '{}'), true) ?: [];
        return $r;
    }
}
