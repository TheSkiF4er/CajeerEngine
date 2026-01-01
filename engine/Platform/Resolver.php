<?php
namespace Platform;

use Database\DB;

class Resolver
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo();
        if (!$pdo) return;
        $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/platform_v2_5.sql'));
    }

    public static function resolveTenant(array $cfg): int
    {
        if (empty($cfg['enabled'])) return 0;

        $mode = (string)($cfg['resolver']['mode'] ?? 'host');

        if ($mode === 'header') {
            $h = (string)($cfg['resolver']['header_name'] ?? 'X-Tenant-Id');
            $key = 'HTTP_' . strtoupper(str_replace('-', '_', $h));
            return (int)($_SERVER[$key] ?? 0);
        }

        if ($mode === 'query') {
            $q = (string)($cfg['resolver']['query_name'] ?? 'tenant_id');
            return (int)($_GET[$q] ?? 0);
        }

        // host mapping
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $host = preg_replace('/:\d+$/', '', $host);

        $pdo = DB::pdo();
        if (!$pdo) return 0;

        // custom domain mapping
        $st = $pdo->prepare("SELECT tenant_id FROM ce_tenant_domains WHERE host=:h LIMIT 1");
        $st->execute([':h'=>$host]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if ($row) return (int)$row['tenant_id'];

        // subdomain mapping (tenant.example.com)
        $base = strtolower((string)($cfg['resolver']['host']['base_domain'] ?? ''));
        if ($base && str_ends_with($host, '.' . $base)) {
            $sub = substr($host, 0, -1 * (strlen($base) + 1));
            $sub = explode('.', $sub)[0] ?? '';
            if ($sub) {
                $st = $pdo->prepare("SELECT id FROM ce_tenants WHERE slug=:s LIMIT 1");
                $st->execute([':s'=>$sub]);
                $row = $st->fetch(\PDO::FETCH_ASSOC);
                if ($row) return (int)$row['id'];
            }
        }

        return 0;
    }

    public static function resolveSite(array $cfg, int $tenantId): int
    {
        if (!$tenantId) return 0;
        $mode = (string)($cfg['site']['mode'] ?? 'host');

        if ($mode === 'header') {
            $h = (string)($cfg['site']['header_name'] ?? 'X-Site-Id');
            $key = 'HTTP_' . strtoupper(str_replace('-', '_', $h));
            return (int)($_SERVER[$key] ?? 0);
        }

        // host mapping: domain -> site_id or ce_sites.host
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $host = preg_replace('/:\d+$/', '', $host);

        $pdo = DB::pdo();
        if (!$pdo) return 0;

        $st = $pdo->prepare("SELECT site_id FROM ce_tenant_domains WHERE host=:h AND tenant_id=:t LIMIT 1");
        $st->execute([':h'=>$host, ':t'=>$tenantId]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if ($row && (int)$row['site_id'] > 0) return (int)$row['site_id'];

        $st = $pdo->prepare("SELECT id FROM ce_sites WHERE tenant_id=:t AND host=:h LIMIT 1");
        $st->execute([':t'=>$tenantId, ':h'=>$host]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if ($row) return (int)$row['id'];

        // fallback first site
        $st = $pdo->prepare("SELECT id FROM ce_sites WHERE tenant_id=:t ORDER BY id ASC LIMIT 1");
        $st->execute([':t'=>$tenantId]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : 0;
    }
}
