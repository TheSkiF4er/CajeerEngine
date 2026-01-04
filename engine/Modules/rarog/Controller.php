<?php
namespace Modules\rarog;

use Template\Template;

class Controller
{
    public function index(): void
    {
        $tpl = new Template();
        $tpl->render('rarog.tpl', [
            'title' => 'Rarog — CajeerEngine',
        ]);
    }
}
