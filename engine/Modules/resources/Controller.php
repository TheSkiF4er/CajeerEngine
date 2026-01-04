<?php
namespace Modules\resources;

use Template\Template;
use Security\Session;

class Controller
{
    private function isLogged(): bool
    {
        Session::start();
        return isset($_SESSION['ce_user']) && is_array($_SESSION['ce_user']);
    }

    public function redirectMarketplace(): void
    {
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/marketplace');
        $path = (string)parse_url($uri, PHP_URL_PATH);

        // /marketplace -> /resources
        $target = '/resources';
        if ($path === '/marketplace/themes') $target = '/resources/themes';
        if ($path === '/marketplace/plugins') $target = '/resources/plugins';
        if ($path === '/marketplace/profile') $target = '/resources/profile';

        header('Location: ' . $target, true, 301);
        exit;
    }

    public function index(): void
    {
        $tpl = new Template();
        $tpl->render('resources.tpl', [
            'title' => 'Ресурсы — CajeerEngine',
            'upload_href' => $this->isLogged() ? '/profile' : '/login?return=%2Fprofile',
        ]);
    }

    public function themes(): void
    {
        $tpl = new Template();
        $tpl->render('resources_list.tpl', [
            'title' => 'Ресурсы: Темы — CajeerEngine',
            'kind' => 'themes',
            'kind_title' => 'Темы',
            'upload_href' => $this->isLogged() ? '/profile' : '/login?return=%2Fprofile',
        ]);
    }

    public function plugins(): void
    {
        $tpl = new Template();
        $tpl->render('resources_list.tpl', [
            'title' => 'Ресурсы: Плагины и модули — CajeerEngine',
            'kind' => 'plugins',
            'kind_title' => 'Плагины и модули',
            'upload_href' => $this->isLogged() ? '/profile' : '/login?return=%2Fprofile',
        ]);
    }

    public function profile(): void
    {
        if ($this->isLogged()) {
            header('Location: /profile', true, 302);
        } else {
            header('Location: /login?return=%2Fprofile', true, 302);
        }
        exit;
    }
}
