<?php
namespace Observability;

class Logger
{
    protected static array $cfg = [];
    protected static bool $inited = false;

    public static function init(array $cfg): void { self::$cfg = $cfg; self::$inited = true; }

    public static function log(string $level, string $message, array $ctx = []): void
    {
        if (!self::$inited || empty(self::$cfg['enabled'])) return;
        $path = (string)(self::$cfg['path'] ?? (ROOT_PATH . '/storage/logs/app.jsonl'));
        @mkdir(dirname($path), 0775, true);

        $entry = [
            'ts' => date('c'),
            'level' => $level,
            'msg' => $message,
            'request_id' => Trace::requestId(),
            'tenant_id' => (int)($_SERVER['CE_TENANT_ID'] ?? 0),
            'site_id' => (int)($_SERVER['CE_SITE_ID'] ?? 0),
            'ctx' => $ctx,
        ];
        @file_put_contents($path, json_encode($entry, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n", FILE_APPEND);
    }

    public static function debug(string $m, array $c=[]): void { self::log('debug',$m,$c); }
    public static function info(string $m, array $c=[]): void { self::log('info',$m,$c); }
    public static function warn(string $m, array $c=[]): void { self::log('warn',$m,$c); }
    public static function error(string $m, array $c=[]): void { self::log('error',$m,$c); }
}
