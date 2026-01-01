<?php
namespace Modules\admin\Controllers;

use Core\Response;
use Marketplace\Client;

class MarketplaceController
{
    public function status()
    {
        $c = new Client();
        Response::json(['marketplace'=>$c->status()]);
    }

    public function themes()
    {
        $c = new \Marketplace\Client();
        \Core\Response::json(['ok'=>true,'themes'=>$c->listThemes()]);
    }

    public function plugins()
    {
        $c = new \Marketplace\Client();
        \Core\Response::json(['ok'=>true,'plugins'=>$c->listPlugins()]);
    }
}
