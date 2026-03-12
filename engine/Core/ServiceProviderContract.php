<?php
namespace Core;

if (!interface_exists(__NAMESPACE__ . '\\ServiceProviderContract', false)) {
    interface ServiceProviderContract
    {
        public function register(KernelContract $kernel): void;
    }
}
