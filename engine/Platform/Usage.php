<?php
namespace Platform;

use Database\DB;

class Usage
{
    public static function inc(string $metricKey, int $delta = 1, ?int $tenantId = null, ?int $siteId = null): void
    {
        $tenantId = $tenantId ?? Context::tenantId();
        $siteId = $siteId ?? Context::siteId();
        if ($tenantId <= 0) return;

        $pdo = DB::pdo();
        if (!$pdo) return;

        $date = date('Y-m-d');
        $pdo->prepare("INSERT INTO ce_usage_metrics(tenant_id, site_id, metric_key, metric_value, bucket_date, updated_at)
            VALUES(:t,:s,:k,:v,:d,NOW())
            ON DUPLICATE KEY UPDATE metric_value=metric_value+:v2, updated_at=NOW()")
            ->execute([
                ':t'=>$tenantId, ':s'=>$siteId ?: null, ':k'=>$metricKey, ':v'=>$delta, ':d'=>$date, ':v2'=>$delta
            ]);
    }

    public static function getToday(string $metricKey, int $tenantId, ?int $siteId=null): int
    {
        $pdo = DB::pdo(); if(!$pdo) return 0;
        $date = date('Y-m-d');
        $st = $pdo->prepare("SELECT metric_value FROM ce_usage_metrics WHERE tenant_id=:t AND site_id ".($siteId? "= :s":"IS NULL")." AND metric_key=:k AND bucket_date=:d LIMIT 1");
        $params = [':t'=>$tenantId, ':k'=>$metricKey, ':d'=>$date];
        if ($siteId) $params[':s']=$siteId;
        $st->execute($params);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return (int)($row['metric_value'] ?? 0);
    }
}
