<?php
namespace Cache;

use Core\Config;

/**
 * Facade for cache backend (file).
 * Supports:
 * - get/set/delete
 * - remember(key, ttl, callback, tags=[])
 * - invalidateTag(tag)
 */
class Cache
{
    private static ?FileCache $driver = null;

    public static function driver(): FileCache
    {
        if (self::$driver) return self::$driver;

        $path = (string)Config::get('cache.path', ROOT_PATH . '/storage/cache');
        $enabled = (bool)Config::get('cache.enabled', true);
        self::$driver = new FileCache($path, $enabled);
        return self::$driver;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::driver()->get($key, $default);
    }

    public static function set(string $key, mixed $value, int $ttl = 0, array $tags = []): void
    {
        self::driver()->set($key, $value, $ttl, $tags);
    }

    public static function delete(string $key): void
    {
        self::driver()->delete($key);
    }

    public static function remember(string $key, int $ttl, callable $cb, array $tags = []): mixed
    {
        return self::driver()->remember($key, $ttl, $cb, $tags);
    }

    public static function invalidateTag(string $tag): void
    {
        self::driver()->invalidateTag($tag);
    }

    public static function clear(): void
    {
        self::driver()->clear();
    }
}
