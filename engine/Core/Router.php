<?php
declare(strict_types=1);

namespace Core;

/**
 * Minimal router for CajeerEngine.
 *
 * - Loads explicit routes from system/routes.php (if present)
 * - Supports module/action fallback: /<module>/<action>
 * - Special-cases /api/* to API\Router (if present)
 */
final class Router
{
    /** @var array<string, array{0:string,1:string}> */
    private array $routes = [];

    public function __construct()
    {
        $routesFile = ROOT_PATH . '/system/routes.php';
        if (is_file($routesFile)) {
            $loaded = require $routesFile;
            if (is_array($loaded)) {
                $this->routes = $loaded;
            }
        }
    }

    public function dispatch(): void
    {
        $uri = (string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = '/' . ltrim($uri, '/');

        // API routing (headless)
        if (function_exists('str_starts_with') && str_starts_with($uri, '/api')) {
            $apiRouter = '\\API\\Router';
            if (class_exists($apiRouter) && method_exists($apiRouter, 'handle')) {
                $apiRouter::handle();
                return;
            }
        }

        // Explicit routes first
        if (isset($this->routes[$uri])) {
            [$module, $action] = $this->routes[$uri];
            $this->dispatchModule((string)$module, (string)$action);
            return;
        }

        // Fallback: /module/action
        $parts = array_values(array_filter(explode('/', trim($uri, '/'))));
        if (count($parts) >= 1) {
            $module = (string)$parts[0];
            $action = (string)($parts[1] ?? 'index');
            $this->dispatchModule($module, $action);
            return;
        }

        $this->notFound();
    }

    private function dispatchModule(string $module, string $action): void
    {
        // Modules namespace convention: Modules\<module>\Controller
        $controllerClass = '\\Modules\\' . $module . '\\Controller';

        if (!class_exists($controllerClass)) {
            $this->notFound("Controller not found: {$controllerClass}");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $this->notFound("Action not found: {$controllerClass}::{$action}()");
            return;
        }

        $controller->{$action}();
    }

    private function notFound(string $msg = '404 Not Found'): void
    {
        if (!headers_sent()) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
        }
        echo $msg;
    }
}
