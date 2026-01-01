<?php
namespace Plugins;

use Core\KernelContract;
use Database\DB;
use Observability\Logger;

class PluginManager
{
    protected string $pluginsDir;
    protected string $manifestFile;
    protected string $coreVersion;

    /**
     * Backward-compatible constructor.
     *
     * Historically the Kernel passed (pluginsDir, manifestFile, coreVersion).
     * Starting from 3.x the manager is kernel-centric.
     */
    public function __construct(
        protected KernelContract $kernel,
        ?string $pluginsDir = null,
        ?string $manifestFile = null,
        ?string $coreVersion = null
    ) {
        $this->pluginsDir = $pluginsDir ?: (ROOT_PATH . '/plugins');
        $this->manifestFile = $manifestFile ?: (ROOT_PATH . '/system/plugins.php');
        $this->coreVersion = $coreVersion ?: $this->kernel->version();
    }

    /**
     * Boot all enabled plugins (foundation).
     *
     * This is intentionally tolerant: if registry tables are not available yet,
     * the platform should continue to boot.
     */
    public function bootEnabled($container = null, $events = null): void
    {
        try {
            // Ensure registry exists and sync filesystem manifests.
            $this->syncRegistry(0);
            $pdo = DB::pdo();
            if (!$pdo) return;

            // Boot enabled plugins (tenant 0 for now).
            $rows = $pdo->query("SELECT slug FROM ce_plugins WHERE tenant_id=0 AND enabled=1 ORDER BY id ASC")
                ?->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            foreach ($rows as $r) {
                $slug = (string)($r['slug'] ?? '');
                if ($slug !== '') {
                    $this->enable($slug, 0);
                }
            }
        } catch (\Throwable $e) {
            // Do not block platform boot due to plugin subsystem.
            Logger::warn('plugins.bootEnabled.failed', ['error'=>$e->getMessage()]);
        }
    }

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/platform_v3_0.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function discover(): array
    {
        $plugins = [];
        $dir = $this->pluginsDir;
        if (!is_dir($dir)) return $plugins;

        foreach (glob($dir . '/*/plugin.json') as $manifestFile) {
            $data = json_decode((string)file_get_contents($manifestFile), true);
            if (!is_array($data)) continue;
            $data['_path'] = dirname($manifestFile);
            $plugins[] = $data;
        }
        return $plugins;
    }

    public function syncRegistry(int $tenantId = 0): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $discovered = $this->discover();
        foreach ($discovered as $m) {
            $slug = (string)($m['slug'] ?? '');
            if ($slug === '') continue;

            $pdo->prepare("INSERT INTO ce_plugins(tenant_id,name,slug,version,enabled,manifest_json,installed_at,updated_at)
                           VALUES(:t,:n,:s,:v,0,:mj,NOW(),NOW())
                           ON DUPLICATE KEY UPDATE name=:n2, version=:v2, manifest_json=:mj2, updated_at=NOW()")
                ->execute([
                    ':t'=>$tenantId,
                    ':n'=>(string)($m['name'] ?? $slug),
                    ':s'=>$slug,
                    ':v'=>(string)($m['version'] ?? '0.0.0'),
                    ':mj'=>json_encode($m, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                    ':n2'=>(string)($m['name'] ?? $slug),
                    ':v2'=>(string)($m['version'] ?? '0.0.0'),
                    ':mj2'=>json_encode($m, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ]);
        }
        return ['ok'=>true,'discovered'=>count($discovered)];
    }

    public function enable(string $slug, int $tenantId = 0): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $row = $pdo->query("SELECT * FROM ce_plugins WHERE tenant_id=".(int)$tenantId." AND slug=".$pdo->quote($slug)." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return ['ok'=>false,'error'=>'not_found'];

        $manifest = json_decode((string)$row['manifest_json'], true) ?: [];
        $providerClass = (string)($manifest['provider'] ?? '');
        $autoload = (string)($manifest['autoload'] ?? '');
        $path = (string)($manifest['_path'] ?? '');

        if ($autoload && $path) {
            $file = $path . '/' . $autoload;
            if (is_file($file)) require_once $file;
        }

        if ($providerClass && class_exists($providerClass)) {
            $provider = new $providerClass();
            $this->kernel->registerProvider($provider);
            $provider->boot($this->kernel);
        }

        $pdo->prepare("UPDATE ce_plugins SET enabled=1, enabled_at=NOW(), updated_at=NOW() WHERE id=:id")->execute([':id'=>(int)$row['id']]);
        $this->kernel->events()->emit('plugin.enabled', ['slug'=>$slug,'tenant_id'=>$tenantId]);

        Logger::info('plugins.enable', ['slug'=>$slug,'tenant'=>$tenantId]);
        return ['ok'=>true];
    }

    public function disable(string $slug, int $tenantId = 0): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $pdo->prepare("UPDATE ce_plugins SET enabled=0, disabled_at=NOW(), updated_at=NOW() WHERE tenant_id=:t AND slug=:s")
            ->execute([':t'=>$tenantId,':s'=>$slug]);

        $this->kernel->events()->emit('plugin.disabled', ['slug'=>$slug,'tenant_id'=>$tenantId]);
        return ['ok'=>true];
    }
}
