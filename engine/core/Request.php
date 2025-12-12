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
}
