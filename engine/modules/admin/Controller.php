<?php
namespace Modules\admin;

use Template\Template;

class Controller
{
    public function index(): void
    {
        $tpl = new Template(ROOT_PATH . '/templates/admin');
        $tpl->render('main.tpl', [
            'title' => 'Admin',
            'content' => 'Admin dashboard (stub)',
        ]);
    }
}
