<?php
declare(strict_types=1);

namespace Modules\auth_local;

use Template\Template;

final class Controller
{
    public function login(): void
    {
        $tpl = new Template(theme: 'default');
        $tpl->render('login.tpl', ['title' => 'Вход']);
    }

    public function register(): void
    {
        $tpl = new Template(theme: 'default');
        $tpl->render('register.tpl', ['title' => 'Регистрация']);
    }
}
