<?php
namespace Core;

use Core\Events\EventBus;
use Plugins\PluginManager;
use Cache\Cache;
use Cache\PageCache;

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

        // services
        $this->container->singleton('seo', function() { return new \Seo\MetaManager(); });

        $coreVersion = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt')) ?: '0.0.0';
        $this->plugins = new PluginManager(ROOT_PATH . '/plugins', ROOT_PATH . '/system/plugins.php', $coreVersion);
        $this->container->instance('plugins', $this->plugins);

        \Core\KernelSingleton::set($this->container, $this->events);
    }

    public function boot(): void
    {
        $this->events->emit('kernel.booting');

        // Page cache (early)
        if (PageCache::eligible()) {
            $cached = Cache::get(PageCache::key(), null);
            if (is_string($cached) && $cached !== '') {
                $this->events->emit('cache.page.hit', ['key'=>PageCache::key()]);
                header('X-Cajeer-Cache: HIT');
                echo $cached;
                return;
            }
        }

        $this->plugins->bootEnabled($this->container, $this->events);
        $this->events->emit('kernel.routing');

        // Capture output to allow post-processing + cache store
        ob_start();
        $router = new Router();
        $router->dispatch();
        $html = (string)ob_get_clean();

        // Lazy-loading post-process
        $html = \Seo\Html::lazyImages($html);

        // store cache if eligible and status 200 and no redirect header
        if (PageCache::eligible()) {
            $code = http_response_code();
            $hasLocation = false;
            foreach (headers_list() as $h) {
                if (stripos($h, 'Location:') === 0) { $hasLocation = true; break; }
            }
            if ($code >= 200 && $code < 300 && !$hasLocation) {
                Cache::set(PageCache::key(), $html, PageCache::ttl(), ['page']);
                header('X-Cajeer-Cache: MISS');
                $this->events->emit('cache.page.store', ['key'=>PageCache::key(), 'ttl'=>PageCache::ttl()]);
            }
        }

        echo $html;

        $this->events->emit('kernel.done');
    }
}
