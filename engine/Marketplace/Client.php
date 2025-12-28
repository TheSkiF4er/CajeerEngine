<?php
namespace Marketplace;

class Client
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = is_file(ROOT_PATH . '/system/marketplace.php')
            ? (array)require ROOT_PATH . '/system/marketplace.php'
            : ['enabled'=>false];
    }

    public function enabled(): bool { return (bool)($this->cfg['enabled'] ?? false); }

    public function status(): array
    {
        return [
            'enabled' => $this->enabled(),
            'endpoint' => (string)($this->cfg['base_url'] ?? ''),
            'note' => 'Marketplace API is a preparation stub in v1.9.',
        ];
    }

    public function listThemes(): array { return $this->status(); }
    public function listPlugins(): array { return $this->status(); }
}
