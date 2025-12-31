<?php
namespace ControlPlane;

use Database\DB;

class FleetManager
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/control_plane_v3_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function register(array $site): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        self::ensureSchema();

        $pdo->prepare("INSERT INTO ce_fleet_sites(tenant_id,site_id,region,role,base_url,status,tags,created_at,updated_at)
                       VALUES(:t,:s,:r,:ro,:b,:st,:tg,NOW(),NOW())
                       ON DUPLICATE KEY UPDATE region=:r2, role=:ro2, base_url=:b2, status=:st2, tags=:tg2, updated_at=NOW()")
            ->execute([
              ':t'=>(int)($site['tenant_id'] ?? 0),
              ':s'=>(int)($site['site_id'] ?? 0),
              ':r'=>(string)($site['region'] ?? 'local'),
              ':ro'=>(string)($site['role'] ?? 'origin'),
              ':b'=>$site['base_url'] ?? null,
              ':st'=>(string)($site['status'] ?? 'active'),
              ':tg'=>$site['tags'] ?? null,
              ':r2'=>(string)($site['region'] ?? 'local'),
              ':ro2'=>(string)($site['role'] ?? 'origin'),
              ':b2'=>$site['base_url'] ?? null,
              ':st2'=>(string)($site['status'] ?? 'active'),
              ':tg2'=>$site['tags'] ?? null,
            ]);
    }

    public static function list(int $tenantId=0, string $status=''): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();

        if ($tenantId > 0 && $status !== '') {
            $st = $pdo->prepare("SELECT * FROM ce_fleet_sites WHERE tenant_id=:t AND status=:s ORDER BY id DESC LIMIT 500");
            $st->execute([':t'=>$tenantId,':s'=>$status]);
        } elseif ($tenantId > 0) {
            $st = $pdo->prepare("SELECT * FROM ce_fleet_sites WHERE tenant_id=:t ORDER BY id DESC LIMIT 500");
            $st->execute([':t'=>$tenantId]);
        } elseif ($status !== '') {
            $st = $pdo->prepare("SELECT * FROM ce_fleet_sites WHERE status=:s ORDER BY id DESC LIMIT 500");
            $st->execute([':s'=>$status]);
        } else {
            $st = $pdo->query("SELECT * FROM ce_fleet_sites ORDER BY id DESC LIMIT 500");
        }

        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
