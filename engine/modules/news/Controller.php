<?php
namespace Modules\news;

use Template\Template;

class Controller
{
    public function index(): void
    {
        $tpl = new Template();
        $tpl->render('main.tpl', [
            'title' => 'CajeerEngine',
            'content' => 'News index (stub)',
        ]);
    }

    public function view(): void
    {
        echo 'News view (stub)';
    }
}
