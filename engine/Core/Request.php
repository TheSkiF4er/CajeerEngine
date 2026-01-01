<?php
namespace Core;

class Request
{
    public static function uri(): string
    {
        return (string)($_SERVER['REQUEST_URI'] ?? '/');
    }

    public static function method(): string
    {
        return (string)($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }
}
