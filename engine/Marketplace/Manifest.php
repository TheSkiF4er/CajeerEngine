<?php
namespace Marketplace;

class Manifest
{
    public array $raw;

    public function __construct(array $raw){ $this->raw = $raw; }

    public static function fromJson(string $json): self
    {
        $arr = json_decode($json, true);
        if (!is_array($arr)) throw new \Exception("Invalid manifest json");
        return new self($arr);
    }

    public function type(): string { return (string)($this->raw['type'] ?? ''); }
    public function name(): string { return (string)($this->raw['name'] ?? ''); }
    public function version(): string { return (string)($this->raw['version'] ?? ''); }
    public function title(): string { return (string)($this->raw['title'] ?? $this->name()); }
    public function publisherId(): string { return (string)($this->raw['publisher']['id'] ?? ''); }
    public function dependencies(): array { return (array)($this->raw['dependencies'] ?? []); }
    public function engineConstraint(): string { return (string)($this->raw['requires']['engine'] ?? '*'); }
    public function signatureB64(): string { return (string)($this->raw['signature']['ed25519'] ?? ''); }

    public function canonicalJson(): string
    {
        $copy = $this->raw;
        unset($copy['signature']);
        return json_encode($copy, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
