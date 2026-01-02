<?php
namespace Core;

class Router
{
    protected array $routes = [];

    public function __construct()
    {
        $routesFile = ROOT_PATH . '/system/routes.php';
        if (file_exists($routesFile)) {
            $loaded = require $routesFile;
            if (is_array($loaded)) $this->routes = $loaded;
        }
    }

    protected function normalize(string $uri): string
    {
        $path = (string)parse_url($uri, PHP_URL_PATH);

        // Ensure leading slash
        if ($path === '' || $path[0] !== '/') $path = '/' . $path;

        // Remove trailing slash (except root)
        if ($path !== '/') $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }

    public function dispatch(): void
    {
        $uri = $this->normalize($_SERVER['REQUEST_URI'] ?? '/');

        // Exact match, then optional trailing-slash alias (for legacy arrays)
        if (!isset($this->routes[$uri])) {
            $alt = ($uri === '/') ? '/' : ($uri . '/');
            if (isset($this->routes[$alt])) $uri = $alt;
        }

        if (isset($this->routes[$uri])) {
            [$controller, $action] = $this->routes[$uri];

            $controllerClass = '\\Modules\\' . $controller . '\\Controller';
            if (class_exists($controllerClass)) {
                $ctrl = new $controllerClass();
                if (method_exists($ctrl, $action)) {
                    $ctrl->{$action}();
                    return;
                }
            }
        }

        // 404 fallback
        http_response_code(404);

        try {
            if (class_exists('\Template\Template')) {
                $tpl = new \Template\Template();
                $tpl->render('404.tpl', [
                    'title' => '404 — Not Found',
                    'seo_title' => '404 — Not Found',
                    'seo_description' => 'Page not found',
                    'seo_canonical' => '',
                    'seo_og' => '',
                    'seo_twitter' => '',
                    'head_extra' => '',
                    'body_extra' => '',
                    'year' => date('Y'),
                    'runtime_mode' => 'web',
                    'app_version' => trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt')) ?: '0.0.0',
                ]);
                return;
            }
        } catch (\Throwable $e) {
            // ignore and fall through
        }

        echo "404 Not Found";
    }
}
