<?php
namespace Dev;

class Logger
{
    public static function write(string $file, string $line): void
    {
        $cfg = self::cfg();
        if (!($cfg['enabled'] ?? false)) return;

        $dir = (string)($cfg['storage'] ?? (ROOT_PATH . '/storage/dev'));
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $path = rtrim($dir,'/') . '/' . $file;
        $ts = date('c');
        @file_put_contents($path, "[$ts] " . $line . PHP_EOL, FILE_APPEND);
    }

    public static function cfg(): array
    {
        $file = ROOT_PATH . '/system/dev.php';
        if (is_file($file)) return (array)require $file;
        return ['enabled'=>false];
    }
}
