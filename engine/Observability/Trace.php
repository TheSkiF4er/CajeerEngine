<?php
namespace Observability;

class Trace
{
    protected static string $rid = '';

    public static function bootstrap(array $cfg): void
    {
        if (empty($cfg['enabled'])) return;
        $h = (string)($cfg['header_request_id'] ?? 'X-Request-Id');
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $h));
        $rid = (string)($_SERVER[$key] ?? '');

        if ($rid === '' || strlen($rid) > 128) $rid = bin2hex(random_bytes(16));
        self::$rid = $rid;

        if (!empty($cfg['propagate_to_response'])) header($h . ': ' . $rid);
    }

    public static function requestId(): string { return self::$rid; }
}
