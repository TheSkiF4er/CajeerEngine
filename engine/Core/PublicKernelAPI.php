<?php
namespace Core;

if (!interface_exists(__NAMESPACE__ . '\\KernelContract', false)) {
    interface KernelContract
    {
        public function version(): string;
        public function registerProvider(ServiceProviderContract $provider): void;
        public function events(): Events\EventBusContract;
        public function jobs(): \Jobs\QueueContract;
        public function get(string $id);
        public function set(string $id, $service): void;
    }
}

if (!interface_exists(__NAMESPACE__ . '\\ServiceProviderContract', false)) {
    interface ServiceProviderContract
    {
        public function register(KernelContract $kernel): void;
    }
}

if (!interface_exists(__NAMESPACE__ . '\\PluginContract', false)) {
    interface PluginContract
    {
        public function manifest(): array;
        public function provider(): ServiceProviderContract;
    }
}
