<?php
namespace Theme;

use Sites\SiteManager;

class ThemeManager
{
    private static ?array $cfg = null;

    public static function cfg(): array
    {
        if (self::$cfg !== null) return self::$cfg;
        $file = ROOT_PATH . '/system/themes.php';
        self::$cfg = is_file($file) ? (array)require $file : [];
        return self::$cfg;
    }

    public static function active(): string
    {
        // site-level theme override
        try {
            $site = SiteManager::resolve();
            $st = $site->theme();
            if ($st) return $st;
        } catch (\Throwable $e) {}

        $cfg = self::cfg();
        return (string)($cfg['active'] ?? 'default');
    }

    public static function themesPath(): string
    {
        return (string)(self::cfg()['themes_path'] ?? (ROOT_PATH . '/templates/themes'));
    }

    public static function list(): array
    {
        $cfg = self::cfg();
        $list = (array)($cfg['themes'] ?? []);
        if (count($list)) return $list;

        $dir = self::themesPath();
        $out = [];
        if (is_dir($dir)) {
            foreach (scandir($dir) as $d) {
                if ($d === '.' || $d === '..') continue;
                if (is_dir($dir . '/' . $d)) $out[$d] = ['title'=>$d];
            }
        }
        return $out;
    }

    public static function themeDir(?string $theme = null): string
    {
        $theme = $theme ?: self::active();
        return rtrim(self::themesPath(), '/') . '/' . $theme;
    }

    public static function templatePath(?string $theme = null): string
    {
        return self::themeDir($theme);
    }

    public static function assetsBase(): string
    {
        return (string)(self::cfg()['assets_base'] ?? '/assets/themes');
    }

    public static function themeUrl(?string $theme = null): string
    {
        $theme = $theme ?: self::active();
        return rtrim(self::assetsBase(), '/') . '/' . $theme;
    }

    public static function switch(string $theme): bool
    {
        $cfg = self::cfg();
        if (!($cfg['allow_switch'] ?? true)) return false;

        $themes = self::list();
        if (!isset($themes[$theme]) && !is_dir(self::themeDir($theme))) return false;

        $cfg['active'] = $theme;
        $php = "<?php\nreturn " . var_export($cfg, true) . ";\n";
        file_put_contents(ROOT_PATH . '/system/themes.php', $php);

        self::$cfg = null;
        return true;
    }
}
