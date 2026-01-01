<?php
namespace AutoUpdate;

class Health
{
    public static function ok(): array
    {
        return [
            'ok' => true,
            'time' => date('c'),
            'php' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
        ];
    }
}
