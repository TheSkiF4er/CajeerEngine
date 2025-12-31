<?php
namespace ControlPlane;

use Database\DB;

class Forecasting
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/control_plane_v3_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
        $sql2 = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql2)) $pdo->exec(file_get_contents($sql2));
    }

    public static function estimate(int $tenantId, int $siteId = 0, int $windowHours = 24): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'no_db'];
        self::ensureSchema();

        $st = $pdo->prepare("SELECT COUNT(*) total,
                                    SUM(CASE WHEN status_code>=500 THEN 1 ELSE 0 END) err_cnt,
                                    AVG(duration_ms) avg_ms
                             FROM ce_perf_requests
                             WHERE tenant_id=:t AND ts >= DATE_SUB(NOW(), INTERVAL :h HOUR)");
        $st->execute([':t'=>$tenantId,':h'=>$windowHours]);
        $row = $st->fetch(\PDO::FETCH_ASSOC) ?: ['total'=>0,'err_cnt'=>0,'avg_ms'=>0];

        $total = max(0, (int)($row['total'] ?? 0));
        $err = (int)($row['err_cnt'] ?? 0);
        $avg = (float)($row['avg_ms'] ?? 0);

        $rps = $windowHours > 0 ? $total / ($windowHours * 3600.0) : 0.0;
        $errRate = $total > 0 ? $err / $total : 0.0;

        // p95 estimate foundation: avg*1.8 fallback
        $p95 = $avg * 1.8;

        $notes = $rps > 50 ? 'Consider horizontal scaling' : 'OK';

        $pdo->prepare("INSERT INTO ce_capacity_forecast(tenant_id,site_id,window_hours,rps_est,p95_ms_est,error_rate_est,notes,created_at)
                       VALUES(:t,:s,:h,:r,:p,:e,:n,NOW())")
            ->execute([':t'=>$tenantId,':s'=>$siteId,':h'=>$windowHours,':r'=>$rps,':p'=>$p95,':e'=>$errRate,':n'=>$notes]);

        return ['ok'=>true,'window_hours'=>$windowHours,'rps_est'=>$rps,'p95_ms_est'=>$p95,'error_rate_est'=>$errRate,'notes'=>$notes];
    }
}
