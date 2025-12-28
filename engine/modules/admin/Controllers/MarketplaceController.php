<?php
namespace Modules\admin\Controllers;

use Core\Response;
use Marketplace\Client;

class MarketplaceController
{
    public function status()
    {
        $c = new Client();
        Response::json(['marketplace' => $c->status()]);
    }
}
