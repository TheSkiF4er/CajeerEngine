<?php
namespace Core;

class Response
{
    public static function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function view(string $content, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
        exit;
    }
}
