<?php
namespace Edge;

use Database\DB;

class RegionRouter
{
    public static function currentRegion(array $cfg): string
    {
        return (string)($cfg['regions']['current'] ?? (getenv('CE_REGION') ?: 'local'));
    }

    public static function role(array $cfg): string
    {
        return (string)($cfg['mode']['role'] ?? 'origin');
    }

    public static function readonlyEdge(array $cfg): bool
    {
        return (bool)($cfg['mode']['readonly_edge'] ?? false);
    }

    public static function shouldCanary(array $cfg): bool
    {
        $c = $cfg['ops']['canary'] ?? [];
        if (!($c['enabled'] ?? true)) return false;
        $percent = (int)($c['percent'] ?? 0);
        if ($percent <= 0) return false;
        $h = (string)($c['header'] ?? 'X-Canary');
        if (isset($_SERVER['HTTP_'.strtoupper(str_replace('-','_',$h))])) return true;

        // deterministic canary by client hash
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $v = hexdec(substr(sha1($ip), 0, 2)) % 100;
        return $v < $percent;
    }

    public static function logDecision(int $tenantId, string $region, string $decision, string $path, int $statusCode = 200, ?int $ms = null): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/edge_v3_8.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));

        $st = $pdo->prepare("INSERT INTO ce_edge_route_logs(tenant_id,region,decision,path,status_code,ms,created_at)
                             VALUES(:t,:r,:d,:p,:s,:m,NOW())");
        $st->execute([':t'=>$tenantId,':r'=>$region,':d'=>$decision,':p'=>$path,':s'=>$statusCode,':m'=>$ms]);
    }
}
