<?php
namespace Admin;

use Core\Config;
use Core\ErrorHandler;
use Core\Container;
use Core\Events\EventBus;
use Plugins\PluginManager;
use Core\KernelContract;
use Core\ServiceProviderContract;

class AdminKernel implements KernelContract
{
    public Container $container;
    public EventBus $events;
    public PluginManager $plugins;

    public function boot(): void
    {
        ErrorHandler::register();
        Config::load();
        date_default_timezone_set((string)Config::get('app.timezone', 'UTC'));

        \Security\Session::start();

        $this->container = new Container();
        $this->events = new EventBus();

        $this->container->instance('container', $this->container);
        $this->container->instance('events', $this->events);

        $this->container->singleton('seo', function() { return new \Seo\MetaManager(); });


        $this->plugins = new PluginManager($this);
        $this->container->instance('plugins', $this->plugins);

        \Core\KernelSingleton::set($this->container, $this->events);

        $this->events->emit('admin.kernel.booting');

        $this->plugins->bootEnabled($this->container, $this->events);

        $router = new Router();
        $router->dispatch();

        $this->events->emit('admin.kernel.done');
    }

    // ---- KernelContract (foundation) ----

    public function version(): string
    {
        $v = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt'));
        return $v !== '' ? $v : '0.0.0';
    }

    public function registerProvider(ServiceProviderContract $provider): void
    {
        $provider->register($this);
    }

    public function events(): \Core\Events\EventBusContract
    {
        return $this->events;
    }

    public function jobs(): \Jobs\QueueContract
    {
        return \Jobs\QueueFactory::make((string)\Core\Config::get('jobs.driver', 'db'));
    }

    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function set(string $id, $service): void
    {
        $this->container->instance($id, $service);
    }
}
