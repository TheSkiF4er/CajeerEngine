<?php
namespace Marketplace;

class Client
{
    protected array $cfg;
    public function __construct(array $cfg){ $this->cfg = $cfg; }

    protected function url(string $path): string
    {
        return rtrim((string)$this->cfg['base_url'], '/') . '/' . ltrim($path, '/');
    }

    public function fetchJson(string $path): array
    {
        $url = $this->url($path);
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) throw new \Exception("Marketplace fetch failed: $url");
        $arr = json_decode($raw, true);
        if (!is_array($arr)) throw new \Exception("Marketplace invalid json");
        return $arr;
    }

    public function index(): array { return $this->fetchJson('index'); }
    public function package(string $type, string $name): array { return $this->fetchJson('packages/'.rawurlencode($type).'/'.rawurlencode($name)); }
}
