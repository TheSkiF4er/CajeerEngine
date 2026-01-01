<?php
namespace Plugins;

use Core\Container;
use Core\Events\EventBus;

interface Plugin
{
    public function boot(Container $c, EventBus $events): void;
    public function install(): void;
    public function disable(): void;
    public function uninstall(): void;
}
