<?php
namespace Security;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Hardened defaults
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
        if (!isset($_SESSION['__csrf'])) {
            $_SESSION['__csrf'] = bin2hex(random_bytes(16));
        }
    }

    public static function csrf(): string
    {
        return (string)($_SESSION['__csrf'] ?? '');
    }

    public static function verifyCsrf(?string $token): bool
    {
        return is_string($token) && hash_equals(self::csrf(), $token);
    }
}
