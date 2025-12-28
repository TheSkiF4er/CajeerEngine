<?php
namespace Sites;

class SiteManager
{
    private static ?array $cfg = null;
    private static ?Site $current = null;

    public static function cfg(): array
    {
        if (self::$cfg !== null) return self::$cfg;
        $file = ROOT_PATH . '/system/sites.php';
        self::$cfg = is_file($file) ? (array)require $file : [];
        return self::$cfg;
    }

    public static function resolve(): Site
    {
        if (self::$current) return self::$current;

        $cfg = self::cfg();
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $host = preg_replace('/:\\d+$/', '', $host);

        $key = (string)($cfg['hosts'][$host] ?? ($cfg['default'] ?? 'main'));
        $siteCfg = (array)($cfg['sites'][$key] ?? []);
        self::$current = new Site($key, $siteCfg);
        return self::$current;
    }
}
