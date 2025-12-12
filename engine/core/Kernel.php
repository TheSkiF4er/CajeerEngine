<?php
namespace Core;

class Kernel
{
    public function __construct()
    {
        ErrorHandler::register();
    }

    public function boot(): void
    {
        $router = new Router();
        $router->dispatch();
    }
}
