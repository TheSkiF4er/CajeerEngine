<?php
declare(strict_types=1);

namespace Modules\admin\Controllers;

use Core\Response;
use Marketplace\Client;

final class MarketplaceController
{
    private function client(): Client
    {
        $cfgFile = ROOT_PATH . '/system/marketplace.php';
        $cfg = is_file($cfgFile) ? (require $cfgFile) : null;
        if (!is_array($cfg)) {
            $cfg = null;
        }
        return new Client($cfg);
    }

    public function status(): void
    {
        $c = $this->client();
        Response::json(['ok' => true, 'marketplace' => $c->status()]);
    }

    public function themes(): void
    {
        $c = $this->client();
        Response::json(['ok' => true, 'themes' => $c->listThemes()]);
    }

    public function plugins(): void
    {
        $c = $this->client();
        Response::json(['ok' => true, 'plugins' => $c->listPlugins()]);
    }
}
