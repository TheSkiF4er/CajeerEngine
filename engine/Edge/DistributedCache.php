<?php
namespace Edge;

use Database\DB;

class DistributedCache
{
    protected array $cfg;

    public function __construct(array $cfg = [])
    {
        $this->cfg = $cfg;
    }

    protected function prefix(): string
    {
        $p = (string)($this->cfg['prefix'] ?? 'ce:');
        return $p;
    }

    public function get(string $key): ?string
    {
        $backend = (string)($this->cfg['backend'] ?? 'redis');
        $k = $this->prefix().$key;

        if ($backend === 'redis' && class_exists('\Redis')) {
            try {
                $r = new \Redis();
                $r->connect((string)(getenv('REDIS_HOST') ?: '127.0.0.1'), (int)(getenv('REDIS_PORT') ?: 6379));
                $v = $r->get($k);
                return $v === false ? null : (string)$v;
            } catch (\Throwable $e) { return null; }
        }

        // fallback: DB kv (foundation)
        $pdo = DB::pdo(); if(!$pdo) return null;
        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_kv (k VARCHAR(190) PRIMARY KEY, v MEDIUMTEXT NULL, exp_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $st = $pdo->prepare("SELECT v FROM ce_kv WHERE k=:k AND (exp_at IS NULL OR exp_at > NOW()) LIMIT 1");
        $st->execute([':k'=>$k]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return $row ? (string)$row['v'] : null;
    }

    public function set(string $key, string $value, int $ttlSec = 60): bool
    {
        $backend = (string)($this->cfg['backend'] ?? 'redis');
        $k = $this->prefix().$key;

        if ($backend === 'redis' && class_exists('\Redis')) {
            try {
                $r = new \Redis();
                $r->connect((string)(getenv('REDIS_HOST') ?: '127.0.0.1'), (int)(getenv('REDIS_PORT') ?: 6379));
                return $r->setex($k, max(1,$ttlSec), $value);
            } catch (\Throwable $e) { return false; }
        }

        $pdo = DB::pdo(); if(!$pdo) return false;
        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_kv (k VARCHAR(190) PRIMARY KEY, v MEDIUMTEXT NULL, exp_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $st = $pdo->prepare("REPLACE INTO ce_kv(k,v,exp_at) VALUES(:k,:v,DATE_ADD(NOW(), INTERVAL :ttl SECOND))");
        return $st->execute([':k'=>$k,':v'=>$value,':ttl'=>max(1,$ttlSec)]);
    }
}
