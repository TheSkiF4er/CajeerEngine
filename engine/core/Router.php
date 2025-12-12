<?php
namespace Core;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $routesFile = ROOT_PATH . '/system/routes.php';
        if (file_exists($routesFile)) {
            $this->routes = require $routesFile;
        }
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (isset($this->routes[$uri])) {
            [$module, $action] = $this->routes[$uri];
            $controllerClass = '\\Modules\\' . $module . '\\Controller';
            if (class_exists($controllerClass)) {
                $ctrl = new $controllerClass();
                if (method_exists($ctrl, $action)) {
                    $ctrl->{$action}();
                    return;
                }
            }
        }

        header('HTTP/1.0 404 Not Found');
        echo '404 Not Found';
    }
}
