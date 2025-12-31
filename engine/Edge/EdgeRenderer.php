<?php
namespace Edge;

class EdgeRenderer
{
    public function __construct(protected DistributedCache $cache, protected array $cfg = []) {}

    protected function cacheKey(string $path): string
    {
        $tenant = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $region = (string)(getenv('CE_REGION') ?: 'local');
        return 'edge:html:'.$tenant.':'.$region.':'.sha1($path);
    }

    public function tryServe(string $path): bool
    {
        if (!($this->cfg['enabled'] ?? true)) return false;
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') return false;

        $key = $this->cacheKey($path);
        $hit = $this->cache->get($key);
        if ($hit === null) return false;

        header('X-CE-Edge-Cache: HIT');
        echo $hit;
        return true;
    }

    public function store(string $path, string $html): void
    {
        if (!($this->cfg['enabled'] ?? true)) return;
        $ttl = (int)($this->cfg['cache_ttl_sec'] ?? 60);
        $key = $this->cacheKey($path);
        $this->cache->set($key, $html, $ttl);
    }
}
