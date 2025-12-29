<?php
namespace Plugins\Hello;

use Core\KernelContract;
use Core\ServiceProviderContract;

class HelloProvider implements ServiceProviderContract
{
    public function id(): string { return 'hello'; }

    public function register(KernelContract $kernel): void
    {
        $kernel->set('hello.service', new HelloService());
    }

    public function boot(KernelContract $kernel): void
    {
        $kernel->events()->on('kernel.boot', function(array $p){
            // noop
        });
    }
}

class HelloService {
    public function ping(): string { return 'pong'; }
}
