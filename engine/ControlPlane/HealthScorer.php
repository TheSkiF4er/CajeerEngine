<?php
namespace ControlPlane;

use Database\DB;

class HealthScorer
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/control_plane_v3_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
        $sql2 = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql2)) $pdo->exec(file_get_contents($sql2));
    }

    public static function compute(int $tenantId, int $siteId = 0, int $windowHours = 24, int $slowMs = 1000, float $errThr = 0.02): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['score'=>100,'details'=>['note'=>'no_db']];
        self::ensureSchema();

        $st = $pdo->prepare("SELECT COUNT(*) total,
                                    SUM(CASE WHEN duration_ms>=:slow THEN 1 ELSE 0 END) slow_cnt,
                                    SUM(CASE WHEN status_code>=500 THEN 1 ELSE 0 END) err_cnt,
                                    AVG(duration_ms) avg_ms,
                                    MAX(duration_ms) mx_ms
                             FROM ce_perf_requests
                             WHERE tenant_id=:t AND ts >= DATE_SUB(NOW(), INTERVAL :h HOUR)");
        $st->execute([':t'=>$tenantId,':h'=>$windowHours,':slow'=>$slowMs]);
        $row = $st->fetch(\PDO::FETCH_ASSOC) ?: ['total'=>0,'slow_cnt'=>0,'err_cnt'=>0,'avg_ms'=>0,'mx_ms'=>0];

        $total = max(0, (int)($row['total'] ?? 0));
        $slow = (int)($row['slow_cnt'] ?? 0);
        $err = (int)($row['err_cnt'] ?? 0);

        $errRate = $total > 0 ? $err / $total : 0.0;
        $slowRate = $total > 0 ? $slow / $total : 0.0;

        $score = 100;
        // penalties
        $score -= (int)round(min(60, $slowRate * 100)); // up to -60
        $score -= (int)round(min(40, max(0, ($errRate - $errThr) * 2000))); // -40 if +2% over threshold
        $score = max(0, min(100, $score));

        $details = [
          'window_hours'=>$windowHours,
          'total'=>$total,
          'slow_cnt'=>$slow,
          'err_cnt'=>$err,
          'err_rate'=>$errRate,
          'slow_rate'=>$slowRate,
          'avg_ms'=>(float)($row['avg_ms'] ?? 0),
          'mx_ms'=>(float)($row['mx_ms'] ?? 0),
        ];

        $pdo->prepare("INSERT INTO ce_platform_health(tenant_id,site_id,score,details_json,window_hours,created_at)
                       VALUES(:t,:s,:sc,:d,:h,NOW())")
            ->execute([
              ':t'=>$tenantId, ':s'=>$siteId, ':sc'=>$score,
              ':d'=>json_encode($details, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
              ':h'=>$windowHours
            ]);

        return ['score'=>$score,'details'=>$details];
    }
}
