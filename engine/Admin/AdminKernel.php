<?php
namespace Admin;

use Core\Config;
use Core\ErrorHandler;

class AdminKernel
{
    public function boot(): void
    {
        ErrorHandler::register();
        Config::load();
        date_default_timezone_set((string)Config::get('app.timezone', 'UTC'));

        \Security\Session::start();

        $router = new Router();
        $router->dispatch();
    }
}
