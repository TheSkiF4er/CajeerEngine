<?php
namespace Languages;

class Language
{
    private static array $data = [];

    public static function load(string $code): void
    {
        $file = ROOT_PATH . '/engine/Languages/' . $code . '/system.lang.php';
        if (file_exists($file)) {
            self::$data = require $file;
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        return (string)(self::$data[$key] ?? $default);
    }
}
