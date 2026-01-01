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


    public function uiBuilderIndex()
    {
        (new \Modules\admin\Controllers\UIBuilderController())->index();
    }

    public function uiBuilderSave()
    {
        (new \Modules\admin\Controllers\UIBuilderController())->save();
    }

    public function marketplaceThemes()
    {
        (new \Modules\admin\Controllers\MarketplaceController())->themes();
    }

    public function marketplacePlugins()
    {
        (new \Modules\admin\Controllers\MarketplaceController())->plugins();
    }
}