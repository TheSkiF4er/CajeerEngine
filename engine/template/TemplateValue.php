<?php
namespace Template;

class TemplateValue
{
    public static function getVar(string $key, array $ctx): string
    {
        return (string)self::dotGet($ctx['vars'] ?? [], $key, '');
    }

    public static function getUser(string $path, array $ctx): string
    {
        return (string)self::dotGet($ctx['user'] ?? [], $path, '');
    }

    public static function getConfig(string $path, array $ctx): string
    {
        return (string)self::dotGet($ctx['config'] ?? [], $path, '');
    }

    private static function dotGet(array $arr, string $path, mixed $default): mixed
    {
        $parts = explode('.', $path);
        $node = $arr;
        foreach ($parts as $p) {
            if (!is_array($node) || !array_key_exists($p, $node)) return $default;
            $node = $node[$p];
        }
        return $node;
    }
}
