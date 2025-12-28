<?php
namespace Plugins;

class PluginManifest
{
    public function __construct(
        public string $id,
        public string $name,
        public string $version,
        public string $mainClass,
        public array $requires = [],
        public array $provides = [],
    ) {}
}
