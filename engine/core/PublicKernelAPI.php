<?php
namespace Core;

interface KernelContract
{
    public function version(): string;
    public function registerProvider(ServiceProviderContract $provider): void;

    public function events(): Events\EventBusContract;
    public function jobs(): Jobs\QueueContract;

    public function get(string $id);
    public function set(string $id, $service): void;
}

interface ServiceProviderContract
{
    public function id(): string;
    public function register(KernelContract $kernel): void;
    public function boot(KernelContract $kernel): void;
}

interface PluginContract
{
    public function slug(): string;
    public function version(): string;
    public function provider(): ServiceProviderContract;
}
