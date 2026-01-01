<?php
namespace ControlPlane;

use Database\DB;

class Insights
{
    protected static function ensureObs(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function crossTenant(int $windowHours = 24): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureObs();

        $st = $pdo->prepare("SELECT tenant_id, COUNT(*) cnt, AVG(duration_ms) avg_ms, MAX(duration_ms) mx_ms
                             FROM ce_perf_requests
                             WHERE ts >= DATE_SUB(NOW(), INTERVAL :h HOUR)
                             GROUP BY tenant_id
                             ORDER BY mx_ms DESC
                             LIMIT 100");
        $st->execute([':h'=>$windowHours]);
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function errorsByTenant(int $windowHours = 24): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureObs();

        $st = $pdo->prepare("SELECT tenant_id,
                                    SUM(CASE WHEN status_code>=500 THEN 1 ELSE 0 END) e5,
                                    COUNT(*) total
                             FROM ce_perf_requests
                             WHERE ts >= DATE_SUB(NOW(), INTERVAL :h HOUR)
                             GROUP BY tenant_id
                             ORDER BY e5 DESC
                             LIMIT 100");
        $st->execute([':h'=>$windowHours]);
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
