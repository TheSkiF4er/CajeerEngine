<?php
namespace Plugins;

use Core\Container;
use Core\Events\EventBus;

class PluginManager
{
    private string $pluginsDir;
    private string $stateFile;
    private string $coreVersion;

    private array $manifests = [];

    public function __construct(string $pluginsDir, string $stateFile, string $coreVersion)
    {
        $this->pluginsDir = rtrim($pluginsDir, '/');
        $this->stateFile = $stateFile;
        $this->coreVersion = $coreVersion;
    }

    public function scan(): void
    {
        $this->manifests = [];
        if (!is_dir($this->pluginsDir)) return;

        foreach (glob($this->pluginsDir . '/*', GLOB_ONLYDIR) as $dir) {
            $mf = $dir . '/plugin.json';
            if (!is_file($mf)) continue;
            $raw = json_decode((string)file_get_contents($mf), true);
            if (!is_array($raw)) continue;

            $id = (string)($raw['id'] ?? basename($dir));
            $this->manifests[$id] = new PluginManifest(
                $id,
                (string)($raw['name'] ?? $id),
                (string)($raw['version'] ?? '0.0.0'),
                (string)($raw['main'] ?? ''),
                (array)($raw['requires'] ?? []),
                (array)($raw['provides'] ?? [])
            );
        }
    }

    public function list(): array
    {
        $this->scan();
        $state = $this->state();
        $out = [];
        foreach ($this->manifests as $id => $m) {
            $out[] = [
                'id'=>$id,
                'name'=>$m->name,
                'version'=>$m->version,
                'enabled'=>(bool)($state['enabled'][$id] ?? false),
                'dir'=>$this->pluginsDir.'/'.$id
            ];
        }
        return $out;
    }

    public function bootEnabled(Container $c, EventBus $events): void
    {
        $this->scan();
        $state = $this->state();
        $enabledIds = array_keys(array_filter($state['enabled'] ?? [], fn($v)=>$v));
        $loadOrder = $this->resolveLoadOrder($enabledIds);

        foreach ($loadOrder as $id) {
            $m = $this->manifests[$id] ?? null;
            if (!$m) continue;

            $this->assertConstraints($m);

            $file = $this->pluginsDir.'/'.$id.'/plugin.php';
            if (is_file($file)) require_once $file;

            $class = $m->mainClass;
            if ($class === '' || !class_exists($class)) {
                throw new \RuntimeException('Plugin main class not found: '.$class.' for '.$id);
            }

            $plugin = new $class();
            if (!$plugin instanceof Plugin) throw new \RuntimeException('Plugin must implement Plugins\\Plugin: '.$id);

            $events->emit('plugins.booting', ['id'=>$id,'manifest'=>$m]);
            $plugin->boot($c, $events);
            $events->emit('plugins.booted', ['id'=>$id,'manifest'=>$m]);

            $c->instance('plugin:'.$id, $plugin);
        }
    }

    public function enable(string $id): void
    {
        $this->scan();
        if (!isset($this->manifests[$id])) throw new \RuntimeException('Plugin not found: '.$id);
        $state = $this->state();
        $state['enabled'][$id] = true;
        $enabledIds = array_keys(array_filter($state['enabled'] ?? [], fn($v)=>$v));
        $this->resolveLoadOrder($enabledIds);
        $this->saveState($state);
    }

    public function disable(string $id): void
    {
        $state = $this->state();
        $state['enabled'][$id] = false;
        $this->saveState($state);
    }

    public function install(string $id): void
    {
        $this->scan();
        $m = $this->manifests[$id] ?? null;
        if (!$m) throw new \RuntimeException('Plugin not found: '.$id);

        $sql = $this->pluginsDir.'/'.$id.'/install.sql';
        if (is_file($sql)) {
            $pdo = \Database\Connection::pdo();
            $pdo->exec((string)file_get_contents($sql));
        }

        $php = $this->pluginsDir.'/'.$id.'/plugin.php';
        if (is_file($php)) require_once $php;
        if ($m->mainClass && class_exists($m->mainClass)) {
            $p = new $m->mainClass();
            if ($p instanceof Plugin) $p->install();
        }
    }

    public function uninstall(string $id): void
    {
        $this->scan();
        $m = $this->manifests[$id] ?? null;
        if (!$m) throw new \RuntimeException('Plugin not found: '.$id);

        $php = $this->pluginsDir.'/'.$id.'/plugin.php';
        if (is_file($php)) require_once $php;
        if ($m->mainClass && class_exists($m->mainClass)) {
            $p = new $m->mainClass();
            if ($p instanceof Plugin) $p->uninstall();
        }

        $sql = $this->pluginsDir.'/'.$id.'/uninstall.sql';
        if (is_file($sql)) {
            $pdo = \Database\Connection::pdo();
            $pdo->exec((string)file_get_contents($sql));
        }
    }

    private function state(): array
    {
        if (!is_file($this->stateFile)) return ['enabled'=>[]];
        $s = require $this->stateFile;
        return is_array($s) ? $s : ['enabled'=>[]];
    }

    private function saveState(array $state): void
    {
        $dir = dirname($this->stateFile);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        file_put_contents($this->stateFile, "<?php
return " . var_export($state, true) . ";
");
    }

    private function assertConstraints(PluginManifest $m): void
    {
        foreach ($m->requires as $k => $constraint) {
            $k = (string)$k; $constraint = (string)$constraint;

            if ($k === 'cajeer') {
                if (!Semver::satisfies($this->coreVersion, $constraint)) {
                    throw new \RuntimeException("Plugin {$m->id} requires cajeer {$constraint}, current {$this->coreVersion}");
                }
                continue;
            }
            if (str_starts_with($k, 'plugin:')) {
                $pid = substr($k, 7);
                $dep = $this->manifests[$pid] ?? null;
                if (!$dep) throw new \RuntimeException("Plugin {$m->id} requires missing {$k}");
                if (!Semver::satisfies($dep->version, $constraint)) {
                    throw new \RuntimeException("Plugin {$m->id} requires {$k} {$constraint}, found {$dep->version}");
                }
            }
        }
    }

    private function resolveLoadOrder(array $enabledIds): array
    {
        $this->scan();
        $enabled = array_flip($enabledIds);

        $edges = [];
        foreach ($enabledIds as $id) {
            $m = $this->manifests[$id] ?? null;
            $deps = [];
            if ($m) {
                foreach ($m->requires as $k=>$c) {
                    $k = (string)$k;
                    if (str_starts_with($k, 'plugin:')) {
                        $pid = substr($k, 7);
                        if (isset($enabled[$pid])) $deps[] = $pid;
                    }
                }
            }
            $edges[$id] = $deps;
        }

        $temp = [];
        $perm = [];
        $order = [];

        $visit = function($n) use (&$visit,&$edges,&$temp,&$perm,&$order) {
            if (isset($perm[$n])) return;
            if (isset($temp[$n])) throw new \RuntimeException('Plugin dependency cycle detected at: '.$n);
            $temp[$n] = true;
            foreach ($edges[$n] ?? [] as $d) $visit($d);
            $perm[$n] = true;
            unset($temp[$n]);
            $order[] = $n;
        };

        foreach ($enabledIds as $id) $visit($id);
        return $order;
    }
}
