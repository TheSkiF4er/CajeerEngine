<?php
namespace Sites;

class Site
{
    public function __construct(public string $key, public array $cfg) {}

    public function title(): string { return (string)($this->cfg['title'] ?? $this->key); }
    public function baseUrl(): string { return (string)($this->cfg['base_url'] ?? ''); }
    public function theme(): string { return (string)($this->cfg['theme'] ?? 'default'); }
    public function storagePrefix(): string { return (string)($this->cfg['storage_prefix'] ?? $this->key); }
    public function dbOverride(): ?array { return is_array($this->cfg['db'] ?? null) ? $this->cfg['db'] : null; }
}
