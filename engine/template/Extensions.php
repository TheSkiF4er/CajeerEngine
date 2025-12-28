<?php
namespace Template;

/**
 * Registry for module tags: {module:name ...}
 */
class Extensions
{
    private static bool $booted = false;
    private static array $modules = [];

    public static function boot(): void
    {
        if (self::$booted) return;
        self::$booted = true;

        // Built-in example handler for {module:news ...}
        self::registerModule('news', function(array $params, array $ctx): string {
            $limit = (int)($params['limit'] ?? 5);
            return '<div class="rg-alert rg-alert-info">Новости (stub) · limit=' . $limit . '</div>';
        });
    }

    public static function registerModule(string $name, callable $handler): void
    {
        self::$modules[$name] = $handler;
    }

    public static function callModule(string $name, array $params, array $ctx): string
    {
        self::boot();
        if (!isset(self::$modules[$name])) {
            return '<!-- module not found: ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' -->';
        }
        return (string)call_user_func(self::$modules[$name], $params, $ctx);
    }
}
