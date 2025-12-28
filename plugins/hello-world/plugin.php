<?php
namespace Plugins\HelloWorld;

use Plugins\Plugin;
use Core\Container;
use Core\Events\EventBus;

class HelloWorldPlugin implements Plugin
{
    public function boot(Container $c, EventBus $events): void
    {
        \Template\Extensions::registerModule('hello', function(array $params, array $ctx): string {
            $t = (string)($params['text'] ?? 'Hello from plugin');
            return '<div class="rg-alert rg-alert-success">Plugin says: ' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '</div>';
        });

        $events->on('admin.dashboard.widgets', function($ev) {
            $widgets = $ev->payload['widgets'] ?? [];
            $widgets[] = '<div class="rg-alert rg-alert-info">HelloWorld plugin: dashboard widget</div>';
            $ev->payload['widgets'] = $widgets;
        }, 10);
    }

    public function install(): void {}
    public function disable(): void {}
    public function uninstall(): void {}
}
