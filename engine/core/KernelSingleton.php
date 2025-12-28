<?php
namespace Core;

use Core\Events\EventBus;

class KernelSingleton
{
    private static ?Container $container = null;
    private static ?EventBus $events = null;

    public static function set(Container $c, EventBus $e): void
    {
        self::$container = $c;
        self::$events = $e;
    }

    public static function container(): Container
    {
        if (!self::$container) throw new \RuntimeException('KernelSingleton container not set');
        return self::$container;
    }

    public static function events(): EventBus
    {
        if (!self::$events) throw new \RuntimeException('KernelSingleton events not set');
        return self::$events;
    }
}
