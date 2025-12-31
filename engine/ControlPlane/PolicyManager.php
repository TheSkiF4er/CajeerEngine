<?php
namespace ControlPlane;

use Database\DB;

class PolicyManager
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/control_plane_v3_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function get(string $scope='global', int $tenantId=0, int $siteId=0, string $key=''): ?array
    {
        $pdo = DB::pdo(); if(!$pdo) return null;
        self::ensureSchema();
        $st = $pdo->prepare("SELECT value_json,version,updated_at FROM ce_platform_policies WHERE scope=:s AND tenant_id=:t AND site_id=:si AND key_name=:k LIMIT 1");
        $st->execute([':s'=>$scope,':t'=>$tenantId,':si'=>$siteId,':k'=>$key]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;
        return [
          'key'=>$key,
          'value'=>json_decode($row['value_json'] ?? '[]', true),
          'version'=>(int)($row['version'] ?? 1),
          'updated_at'=>$row['updated_at'] ?? null,
        ];
    }

    public static function set(string $scope, int $tenantId, int $siteId, string $key, array $value): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        self::ensureSchema();
        $json = json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $pdo->prepare("INSERT INTO ce_platform_policies(scope,tenant_id,site_id,key_name,value_json,version,created_at,updated_at)
                       VALUES(:s,:t,:si,:k,:v,1,NOW(),NOW())
                       ON DUPLICATE KEY UPDATE value_json=:v2, version=version+1, updated_at=NOW()")
            ->execute([':s'=>$scope,':t'=>$tenantId,':si'=>$siteId,':k'=>$key,':v'=>$json,':v2'=>$json]);
    }

    public static function resolve(array $defaults, int $tenantId, int $siteId): array
    {
        $out = $defaults;

        $g = self::get('global', 0, 0, 'policies');
        if ($g && is_array($g['value'])) $out = array_replace_recursive($out, $g['value']);

        $t = self::get('tenant', $tenantId, 0, 'policies');
        if ($t && is_array($t['value'])) $out = array_replace_recursive($out, $t['value']);

        $s = self::get('site', $tenantId, $siteId, 'policies');
        if ($s && is_array($s['value'])) $out = array_replace_recursive($out, $s['value']);

        return $out;
    }
}
