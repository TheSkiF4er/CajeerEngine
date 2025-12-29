<?php
namespace Core;

use Core\Events\EventBus;
use Core\Jobs\DBQueue;
use Plugins\PluginManager;

class Kernel implements KernelContract
{
    protected array $container = [];
    protected Events\EventBusContract $events;
    protected Jobs\QueueContract $jobs;

    public function __construct()
    {
        $this->events = new EventBus();
        $this->jobs = new DBQueue();

        $this->set('events', $this->events);
        $this->set('jobs', $this->jobs);
    }

    public function version(): string
    {
        $vFile = ROOT_PATH . '/system/version.txt';
        return is_file($vFile) ? trim((string)file_get_contents($vFile)) : '3.0.0';
    }

    public function registerProvider(ServiceProviderContract $provider): void
    {
        $provider->register($this);
    }

    public function events(): Events\EventBusContract { return $this->events; }
    public function jobs(): Jobs\QueueContract { return $this->jobs; }

    public function get(string $id) { return $this->container[$id] ?? null; }
    public function set(string $id, $service): void { $this->container[$id] = $service; }

    public function boot()
    {
        // DB init
        if (class_exists('Database\\DB')) {
            $cfg = require ROOT_PATH . '/system/config.php';
            \Database\DB::connect($cfg['db']);
        }

        // Platform schema
        $pdo = \Database\DB::pdo();
        if ($pdo && is_file(ROOT_PATH . '/system/sql/platform_v3_0.sql')) {
            $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/platform_v3_0.sql'));
        }

        // Plugin-first registry
        if (class_exists(PluginManager::class)) {
            $pm = new PluginManager($this);
            $this->set('plugins', $pm);
            $pm->syncRegistry((int)($_SERVER['CE_TENANT_ID'] ?? 0));
        }

        $this->events->emit('kernel.boot', ['version'=>$this->version()]);

        // Router dispatch (skip in CLI)
        if (php_sapi_name() !== 'cli') {
            $router = new Router();
            $router->dispatch();
        }
    }
}
