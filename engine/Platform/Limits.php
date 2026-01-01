<?php
namespace Platform;

use Database\DB;

class Limits
{
    public static function plan(array $cfg, int $tenantId): array
    {
        $plans = (array)($cfg['limits']['plans'] ?? []);
        $default = (string)($cfg['limits']['default_plan'] ?? 'free');

        $pdo = DB::pdo();
        $plan = $default;
        if ($pdo) {
            $st = $pdo->prepare("SELECT plan FROM ce_tenants WHERE id=:id LIMIT 1");
            $st->execute([':id'=>$tenantId]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            if ($row && !empty($row['plan'])) $plan = (string)$row['plan'];
        }
        return (array)($plans[$plan] ?? ($plans[$default] ?? []));
    }

    public static function enforceApiRpm(array $cfg, int $tenantId): bool
    {
        if (empty($cfg['limits']['enabled'])) return true;
        $plan = self::plan($cfg, $tenantId);
        $rpm = (int)($plan['api_rpm'] ?? 0);
        if ($rpm <= 0) return true;

        $current = Usage::getToday('api_requests', $tenantId, null);
        // rpm is per-minute, but we store per-day metric; for skeleton keep soft check
        return $current < ($rpm * 1440);
    }
}
