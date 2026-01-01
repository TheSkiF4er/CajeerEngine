<?php
namespace Template\DSL;

use Theme\ThemeManager;

class Runtime
{
    public static function value(string $key, array $vars)
    {
        if (str_starts_with($key, 'user.')) {
            $u = (array)($vars['user'] ?? []);
            return (string)($u[substr($key,5)] ?? '');
        }
        if (str_starts_with($key, 'config.')) {
            return (string)(\Core\Config::get(substr($key,7)) ?? '');
        }
        if (str_starts_with($key, 'site.')) {
            try {
                $site = \Sites\SiteManager::resolve();
                $k = substr($key,5);
                return match($k) {
                    'key' => $site->key,
                    'title' => $site->title(),
                    'base_url' => $site->baseUrl(),
                    'theme' => $site->theme(),
                    default => '',
                };
            } catch (\Throwable $e) { return ''; }
        }
        return (string)($vars[$key] ?? '');
    }

    public static function includeFile(string $file, array $vars): string
    {
        $tpl = new \Template\Template(ThemeManager::templatePath());
        ob_start();
        $tpl->render($file, $vars);
        return (string)ob_get_clean();
    }

    public static function cond(string $expr, array $vars): bool
    {
        $expr = trim($expr);
        if (preg_match('/^empty\(([^\)]+)\)$/i', $expr, $m)) return self::value(trim($m[1]), $vars) === '';
        if (preg_match('/^!empty\(([^\)]+)\)$/i', $expr, $m)) return self::value(trim($m[1]), $vars) !== '';
        if (preg_match('/^([^=\s]+)\s*==\s*"([^"]*)"$/', $expr, $m)) return (string)self::value($m[1], $vars) === (string)$m[2];
        if (preg_match('/^([^!\s]+)\s*!=\s*"([^"]*)"$/', $expr, $m)) return (string)self::value($m[1], $vars) !== (string)$m[2];
        return self::value($expr, $vars) !== '';
    }

    public static function groupCheck(string $kind, string $groupsCsv, array $vars): bool
    {
        $user = (array)($vars['user'] ?? []);
        $gid = (string)($user['group_id'] ?? '');
        $allowed = array_map('trim', explode(',', $groupsCsv));
        $in = in_array($gid, $allowed, true);
        return $kind === 'group' ? $in : !$in;
    }

    public static function available(string $what): bool { return true; }

    public static function module(string $name, string $args, array $vars): string
    {
        return "<!-- module:$name $args -->";
    }
}
