<?php
namespace Core;

class Deprecation
{
    public static function warn(string $id, string $message, array $meta = []): void
    {
        $payload = ['id'=>$id,'message'=>$message,'meta'=>$meta,'ts'=>date('c')];
        @trigger_error('[CE-DEPRECATED] '.$id.' '.$message, E_USER_DEPRECATED);

        $logDir = ROOT_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
        @file_put_contents($logDir.'/deprecations.log', json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).PHP_EOL, FILE_APPEND);
    }

    public static function enforceInCi(): bool
    {
        return (string)($_ENV['CE_DEPRECATIONS_AS_ERRORS'] ?? getenv('CE_DEPRECATIONS_AS_ERRORS') ?? '') === '1';
    }
}
