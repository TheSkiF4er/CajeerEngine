<?php
namespace Admin;

use Core\Request;
use Security\Auth;
use Security\Rbac;

class Router
{
    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?: '/admin';

        // normalize: /admin, /admin/login, /admin/content, ...
        $path = $uri;

        // auth routes
        if ($path === '/admin/login') {
            (new Controllers\AuthController())->login();
            return;
        }
        if ($path === '/admin/logout') {
            (new Controllers\AuthController())->logout();
            return;
        }

        // require auth for rest
        if (!Auth::check()) {
            header('Location: /admin/login');
            exit;
        }

        // RBAC: map path => permission
        $perm = match ($path) {
            '/admin' => 'admin.dashboard',
            '/admin/content' => 'content.read',
            '/admin/content/create' => 'content.write',
            '/admin/content/edit' => 'content.write',
            '/admin/content/delete' => 'content.write',
            '/admin/templates' => 'templates.read',
            '/admin/templates/edit' => 'templates.write',
            '/admin/users' => 'users.read',
            '/admin/users/edit' => 'users.write',
            '/admin/logs' => 'logs.read',
            default => 'admin.dashboard',
        };

        if (!Rbac::allow($perm)) {
            (new Controllers\UiController())->error('Доступ запрещён', 403);
            return;
        }

        switch ($path) {
            case '/admin':
                (new Controllers\DashboardController())->index();
                return;

            case '/admin/content':
                (new Controllers\ContentController())->index();
                return;
            case '/admin/content/create':
                (new Controllers\ContentController())->create();
                return;
            case '/admin/content/edit':
                (new Controllers\ContentController())->edit();
                return;
            case '/admin/content/delete':
                (new Controllers\ContentController())->delete();
                return;

            case '/admin/templates':
                (new Controllers\TemplateController())->index();
                return;
            case '/admin/templates/edit':
                (new Controllers\TemplateController())->edit();
                return;

            case '/admin/users':
                (new Controllers\UserController())->index();
                return;
            case '/admin/users/edit':
                (new Controllers\UserController())->edit();
                return;

            case '/admin/logs':
                (new Controllers\LogController())->index();
                return;
        }

        (new Controllers\UiController())->error('Страница не найдена', 404);
    }
}
