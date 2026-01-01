<?php
namespace Modules\arog;

use Template\Template;

class Controller
{
    public function index(): void
    {
        $tpl = new Template();
        $tpl->render('arog.tpl', [
            'title' => 'Arog — UI / UX Stack',
        ]);
    }
}
