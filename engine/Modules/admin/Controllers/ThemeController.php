<?php
namespace Modules\admin\Controllers;

use Core\Response;
use Theme\ThemeManager;

class ThemeController
{
    public function index()
    {
        Response::json([
            'active' => ThemeManager::active(),
            'themes' => ThemeManager::list(),
            'hint' => 'POST /admin/themes/switch?theme=slug',
        ]);
    }

    public function switch()
    {
        $theme = (string)($_POST['theme'] ?? $_GET['theme'] ?? '');
        if (!$theme) Response::json(['ok'=>false,'error'=>'theme required']);

        $ok = ThemeManager::switch($theme);
        Response::json(['ok'=>$ok,'active'=>ThemeManager::active()]);
    }
}
