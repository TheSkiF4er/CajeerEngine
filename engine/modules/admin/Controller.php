<?php
namespace Modules\admin;

class Controller
{
    public function index(): void
    {
        header('Location: /admin');
        exit;
    }

    public function themesIndex()
    {
        (new \Modules\admin\Controllers\ThemeController())->index();
    }

    public function themesSwitch()
    {
        (new \Modules\admin\Controllers\ThemeController())->switch();
    }

    public function marketplaceStatus()
    {
        (new \Modules\admin\Controllers\MarketplaceController())->status();
    }

}