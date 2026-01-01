<?php
namespace Intelligence;

use Core\KernelContract;
use Core\ServiceProviderContract;

class IntelligenceServiceProvider implements ServiceProviderContract
{
    public function id(): string { return 'intelligence'; }

    public function register(KernelContract $kernel): void
    {
        $cfg = is_file(ROOT_PATH . '/system/intelligence.php') ? require ROOT_PATH . '/system/intelligence.php' : [];
        $kernel->set('analytics', new Analytics($cfg));
        $kernel->set('perf', new Performance($cfg));
        $kernel->set('cost', new Cost($cfg));
        $kernel->set('automation', new \Automation\PolicyEngine($cfg));
        $kernel->set('ai', new \AIAssist\AIClient($cfg));
    }

    public function boot(KernelContract $kernel): void {}
}
