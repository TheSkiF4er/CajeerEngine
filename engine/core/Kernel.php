<?php
namespace Core;

use Core\Events\EventBus;
use Core\Events\AsyncEventBus;
use Core\Jobs\QueueFactory;
use Plugins\PluginManager;

class Kernel implements KernelContract
{
    protected array $container = [];
    protected Events\EventBusContract $events;
    protected Jobs\QueueContract $jobs;

    public function __construct()
    {
        $syncBus = new EventBus();
        $this->set('events_sync', $syncBus);

        $qcfg = is_file(ROOT_PATH . '/system/queue.php') ? require ROOT_PATH . '/system/queue.php' : ['driver'=>'db'];
        $this->jobs = QueueFactory::fromConfig($qcfg);

        $this->events = new AsyncEventBus($syncBus, $this->jobs, true);

        $this->set('events', $this->events);
        $this->set('jobs', $this->jobs);

        $GLOBALS['CE_KERNEL'] = $this;

        // Marketplace (3.2)
        if (class_exists('Marketplace\\MarketplaceServiceProvider')) {
            $this->registerProvider(new \\Marketplace\\MarketplaceServiceProvider());
        }
    }

    public function version(): string
    {
        $vFile = ROOT_PATH . '/system/version.txt';
        return is_file($vFile) ? trim((string)file_get_contents($vFile)) : '3.1.0';
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
        if (class_exists('Database\\DB')) {
            $cfg = require ROOT_PATH . '/system/config.php';
            \Database\DB::connect($cfg['db']);
        }

        $pdo = \Database\DB::pdo();
        if ($pdo) {
            foreach (['platform_v3_0.sql','async_v3_1.sql'] as $sqlFile) {
                $p = ROOT_PATH . '/system/sql/' . $sqlFile;
                if (is_file($p)) $pdo->exec(file_get_contents($p));
            }
        }

        if (class_exists(PluginManager::class)) {
            $pm = new PluginManager($this);
            $this->set('plugins', $pm);
            $pm->syncRegistry((int)($_SERVER['CE_TENANT_ID'] ?? 0));
        }

        $this->get('events_sync')->emit('kernel.boot', ['version'=>$this->version()]);
        $this->events->emit('kernel.boot_async', ['version'=>$this->version()]);

        if (php_sapi_name() !== 'cli') {
            $router = new Router();
            $router->dispatch();
        }
    }
}
