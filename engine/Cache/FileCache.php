<?php
namespace Cache;

/**
 * File cache with tag index.
 * Stores entries as JSON with expire_at and payload (string/array).
 * Tag invalidation: tags map to list of keys.
 */
class FileCache
{
    private string $path;
    private bool $enabled;

    public function __construct(string $path, bool $enabled = true)
    {
        $this->path = rtrim($path, '/');
        $this->enabled = $enabled;

        if (!is_dir($this->path)) @mkdir($this->path, 0775, true);
        if (!is_dir($this->path . '/_tags')) @mkdir($this->path . '/_tags', 0775, true);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) return $default;

        $f = $this->file($key);
        if (!is_file($f)) return $default;

        $raw = json_decode((string)@file_get_contents($f), true);
        if (!is_array($raw)) return $default;

        $exp = (int)($raw['expire_at'] ?? 0);
        if ($exp > 0 && $exp < time()) {
            @unlink($f);
            return $default;
        }
        return $raw['value'] ?? $default;
    }

    public function set(string $key, mixed $value, int $ttl = 0, array $tags = []): void
    {
        if (!$this->enabled) return;

        $expireAt = $ttl > 0 ? (time() + $ttl) : 0;
        $payload = [
            'expire_at' => $expireAt,
            'value' => $value,
            'tags' => array_values(array_unique(array_map('strval', $tags))),
        ];
        file_put_contents($this->file($key), json_encode($payload, JSON_UNESCAPED_UNICODE));

        foreach ($payload['tags'] as $t) {
            $this->addTagKey($t, $key);
        }
    }

    public function delete(string $key): void
    {
        @unlink($this->file($key));
    }

    public function remember(string $key, int $ttl, callable $cb, array $tags = []): mixed
    {
        $v = $this->get($key, null);
        if ($v !== null) return $v;
        $v = $cb();
        $this->set($key, $v, $ttl, $tags);
        return $v;
    }

    public function invalidateTag(string $tag): void
    {
        $tag = (string)$tag;
        $tf = $this->tagFile($tag);
        if (!is_file($tf)) return;

        $keys = json_decode((string)@file_get_contents($tf), true);
        if (is_array($keys)) {
            foreach ($keys as $k) {
                if (is_string($k)) $this->delete($k);
            }
        }
        @unlink($tf);
    }

    public function clear(): void
    {
        if (!is_dir($this->path)) return;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $f) {
            if ($f->isDir()) @rmdir($f->getPathname());
            else @unlink($f->getPathname());
        }
        @mkdir($this->path, 0775, true);
        @mkdir($this->path . '/_tags', 0775, true);
    }

    private function file(string $key): string
    {
        return $this->path . '/' . sha1($key) . '.json';
    }

    private function tagFile(string $tag): string
    {
        return $this->path . '/_tags/' . sha1($tag) . '.json';
    }

    private function addTagKey(string $tag, string $key): void
    {
        $tf = $this->tagFile($tag);
        $keys = [];
        if (is_file($tf)) {
            $keys = json_decode((string)@file_get_contents($tf), true);
            if (!is_array($keys)) $keys = [];
        }
        if (!in_array($key, $keys, true)) $keys[] = $key;
        file_put_contents($tf, json_encode($keys, JSON_UNESCAPED_UNICODE));
    }
}
