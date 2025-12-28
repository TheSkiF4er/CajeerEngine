<?php
namespace Core;

use Core\Events\EventBus;
use Plugins\PluginManager;

class Kernel
{
    public Container $container;
    public EventBus $events;
    public PluginManager $plugins;

    public function __construct()
    {
        ErrorHandler::register();
        Config::load();
        date_default_timezone_set((string)Config::get('app.timezone', 'UTC'));

        $this->container = new Container();
        $this->events = new EventBus();

        $this->container->instance('container', $this->container);
        $this->container->instance('events', $this->events);

        $coreVersion = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt')) ?: '0.0.0';
        $this->plugins = new PluginManager(ROOT_PATH . '/plugins', ROOT_PATH . '/system/plugins.php', $coreVersion);
        $this->container->instance('plugins', $this->plugins);

        \Core\KernelSingleton::set($this->container, $this->events);
    }

    public function boot(): void
    {
        $this->events->emit('kernel.booting');

        $this->plugins->bootEnabled($this->container, $this->events);

        $this->events->emit('kernel.routing');

        $router = new Router();
        $router->dispatch();

        $this->events->emit('kernel.done');
    }
}
