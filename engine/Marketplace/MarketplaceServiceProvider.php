<?php
namespace Marketplace;

use Core\KernelContract;
use Core\ServiceProviderContract;

class MarketplaceServiceProvider implements ServiceProviderContract
{
    public function id(): string { return 'marketplace'; }

    public function register(KernelContract $kernel): void
    {
        $cfg = is_file(ROOT_PATH . '/system/marketplace.php') ? require ROOT_PATH . '/system/marketplace.php' : [];
        $kernel->set('marketplace', new PackageManager($cfg));
    }

    public function boot(KernelContract $kernel): void {}
}
