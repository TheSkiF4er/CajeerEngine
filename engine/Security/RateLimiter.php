<?php
namespace Security;
use Database\DB;

class RateLimiter
{
    public static function check(string $key, array $cfg): bool
    {
        if (empty($cfg['enabled'])) return true;
        $window = (int)($cfg['window'] ?? 60);
        $max = (int)($cfg['max'] ?? 120);

        $pdo = DB::pdo();
        if (!$pdo) return true; // no DB -> allow

        $now = time();
        $bucket = (int)floor($now / $window);

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_rate_limit (
            rl_key VARCHAR(190) NOT NULL,
            bucket INT NOT NULL,
            hits INT NOT NULL DEFAULT 0,
            updated_at DATETIME NULL,
            PRIMARY KEY (rl_key, bucket)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $st = $pdo->prepare("SELECT hits FROM ce_rate_limit WHERE rl_key=:k AND bucket=:b LIMIT 1");
        $st->execute([':k'=>$key, ':b'=>$bucket]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);

        $hits = (int)($row['hits'] ?? 0);
        if ($hits >= $max) return false;

        $pdo->prepare("INSERT INTO ce_rate_limit(rl_key,bucket,hits,updated_at)
          VALUES(:k,:b,1,NOW())
          ON DUPLICATE KEY UPDATE hits=hits+1,updated_at=NOW()")->execute([':k'=>$key, ':b'=>$bucket]);

        return true;
    }
}
