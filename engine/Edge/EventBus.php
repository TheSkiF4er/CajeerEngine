<?php
namespace Edge;

use Database\DB;

class EventBus
{
    protected array $cfg;
    public function __construct(array $cfg = []) { $this->cfg = $cfg; }

    protected function topic(string $name): string
    {
        $p = (string)($this->cfg['topic_prefix'] ?? 'ce:bus:');
        return $p.$name;
    }

    public function publish(string $name, array $event): bool
    {
        $backend = (string)($this->cfg['backend'] ?? 'redis');
        $topic = $this->topic($name);
        $payload = json_encode(['ts'=>date('c'), 'event'=>$event], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        if ($backend === 'redis' && class_exists('\Redis')) {
            try {
                $r = new \Redis();
                $r->connect((string)(getenv('REDIS_HOST') ?: '127.0.0.1'), (int)(getenv('REDIS_PORT') ?: 6379));
                $r->publish($topic, $payload);
                return true;
            } catch (\Throwable $e) { return false; }
        }

        // DB fallback (foundation)
        $pdo = DB::pdo(); if(!$pdo) return false;
        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_eventbus (id BIGINT AUTO_INCREMENT PRIMARY KEY, topic VARCHAR(190), payload MEDIUMTEXT, created_at DATETIME NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $st = $pdo->prepare("INSERT INTO ce_eventbus(topic,payload,created_at) VALUES(:t,:p,NOW())");
        return $st->execute([':t'=>$topic,':p'=>$payload]);
    }
}
