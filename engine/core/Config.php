<?php
namespace Core;

class Config
{
    public static function get(string $path, $default=null)
    {
        $cfg = $GLOBALS['CE_CONFIG'] ?? null;
        if (!is_array($cfg)) return $default;
        $parts = explode('.', $path);
        $cur = $cfg;
        foreach ($parts as $p) {
            if (!is_array($cur) || !array_key_exists($p, $cur)) return $default;
            $cur = $cur[$p];
        }
        return $cur;
    }

    public static function validateOrDie(): void
    {
        $cfg = $GLOBALS['CE_CONFIG'] ?? null;
        if (!is_array($cfg)) return;

        $errs = [];
        if (empty($cfg['db']['driver'])) $errs[] = 'db.driver required';
        if (empty($cfg['db']['host'])) $errs[] = 'db.host required';
        if (empty($cfg['db']['database'])) $errs[] = 'db.database required';

        if ($errs) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok'=>false,'error'=>'invalid_config','details'=>$errs], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}
