<?php
namespace Core;

if (!interface_exists(__NAMESPACE__ . '\\PluginContract', false)) {
    interface PluginContract
    {
        public function manifest(): array;
        public function provider(): ServiceProviderContract;
    }
}
