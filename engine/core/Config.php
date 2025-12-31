<?php
namespace Core;

class Config
{
    private static array $data = [];

    public static function load(): void
    {
        $cfg = require ROOT_PATH . '/system/config.php';
        self::$data = is_array($cfg) ? $cfg : [];
    }

    public static function all(): array
    {
        return self::$data;
    }

    public static function get(string $path, mixed $default = null): mixed
    {
        $parts = explode('.', $path);
        $node = self::$data;
        foreach ($parts as $p) {
            if (!is_array($node) || !array_key_exists($p, $node)) {
                return $default;
            }
            $node = $node[$p];
        }
        return $node;
    }
}
