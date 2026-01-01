<?php
namespace Core;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException(\Throwable $e): void
    {
        if (ini_get('display_errors')) {
            echo "Exception: " . $e->getMessage();
        }
        // TODO: log to storage/logs
    }
}
