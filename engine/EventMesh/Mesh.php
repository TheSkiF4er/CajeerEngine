<?php
namespace EventMesh;

use Database\DB;

class Mesh
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/platform_v4_0.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function publish(string $topic, array $payload): bool
    {
        $pdo = DB::pdo(); if(!$pdo) return false;
        self::ensureSchema();
        $j = json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $st = $pdo->prepare("INSERT INTO ce_event_mesh(topic,payload,created_at) VALUES(:t,:p,NOW())");
        return $st->execute([':t'=>$topic,':p'=>$j]);
    }

    public static function recent(string $topic = '', int $limit = 50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));
        if ($topic !== '') {
            $st = $pdo->prepare("SELECT * FROM ce_event_mesh WHERE topic=:t ORDER BY id DESC LIMIT $limit");
            $st->execute([':t'=>$topic]);
        } else {
            $st = $pdo->query("SELECT * FROM ce_event_mesh ORDER BY id DESC LIMIT $limit");
        }
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
