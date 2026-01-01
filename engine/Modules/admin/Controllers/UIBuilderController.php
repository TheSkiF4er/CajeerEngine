<?php
namespace Modules\admin\Controllers;

use Core\Response;
use API\Auth;

class UIBuilderController
{
    public function index()
    {
        Auth::require('read');
        $b = new \UIBuilder\Builder();
        Response::json(['ok'=>true,'enabled'=>$b->enabled(),'items'=>$b->list()]);
    }

    public function save()
    {
        Auth::require('write');
        $name = (string)($_POST['name'] ?? $_GET['name'] ?? 'page');
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (!is_array($json)) $json = ['name'=>$name,'blocks'=>[]];

        $b = new \UIBuilder\Builder();
        $file = $b->save($name, $json);
        Response::json(['ok'=>true,'file'=>$file]);
    }
}
