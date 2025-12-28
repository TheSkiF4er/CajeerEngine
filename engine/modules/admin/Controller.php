<?php
namespace Modules\admin;

class Controller
{
    public function index(): void
    {
        header('Location: /admin');
        exit;
    }
}
