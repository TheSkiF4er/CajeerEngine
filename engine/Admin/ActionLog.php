<?php
namespace Admin;

use Database\Connection;
use Security\Auth;

class ActionLog
{
    public static function write(string $action, string $entity, ?int $entityId, array $meta = []): void
    {
        $u = Auth::user();
        $pdo = Connection::pdo();
        $st = $pdo->prepare('INSERT INTO action_logs (user_id, username, action, entity, entity_id, meta_json, ip, created_at) VALUES (:uid,:un,:a,:e,:eid,:m,:ip,NOW())');
        $st->execute([
            'uid' => (int)($u['user_id'] ?? 0),
            'un' => (string)($u['username'] ?? ''),
            'a' => $action,
            'e' => $entity,
            'eid' => $entityId,
            'm' => json_encode($meta, JSON_UNESCAPED_UNICODE),
            'ip' => (string)($_SERVER['REMOTE_ADDR'] ?? ''),
        ]);
    }
}
