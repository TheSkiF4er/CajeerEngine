<?php
namespace Cache;

use Core\Config;
use Security\Auth;

class PageCache
{
    public static function eligible(): bool
    {
        if (!(bool)Config::get('cache.enabled', true)) return false;
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') return false;

        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        if (str_starts_with($uri, '/admin')) return false;

        // if user logged in (admin session), skip
        if (class_exists(Auth::class) && Auth::check()) return false;

        // avoid caching login-related pages
        if (str_contains($uri, '/login')) return false;

        return true;
    }

    public static function key(): string
    {
        $base = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $accept = (string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
        return 'page:' . $base . ':' . $uri . ':' . sha1($accept);
    }

    public static function ttl(): int
    {
        return (int)Config::get('cache.page_ttl', 60);
    }
}
