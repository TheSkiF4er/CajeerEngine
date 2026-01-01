<?php
namespace Dev;

class Collector
{
    private static array $data = [
        'started_at' => 0.0,
        'request' => [],
        'templates' => [],
        'sql' => [],
        'notes' => [],
    ];

    public static function start(): void
    {
        self::$data['started_at'] = microtime(true);
        self::$data['request'] = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];
    }

    public static function note(string $k, $v): void
    {
        self::$data['notes'][$k] = $v;
    }

    public static function template(string $name, float $ms, array $varsKeys = []): void
    {
        self::$data['templates'][] = ['name'=>$name, 'ms'=>$ms, 'vars'=>$varsKeys];
    }

    public static function sql(string $query, float $ms): void
    {
        self::$data['sql'][] = ['q'=>$query, 'ms'=>$ms];
    }

    public static function finish(): array
    {
        $total = (microtime(true) - (float)self::$data['started_at']) * 1000.0;
        self::$data['total_ms'] = round($total, 3);
        return self::$data;
    }
}
