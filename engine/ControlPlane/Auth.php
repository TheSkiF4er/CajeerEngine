<?php
namespace ControlPlane;

class Auth
{
    public static function requireToken(array $cfg): void
    {
        $expected = (string)($cfg['auth']['token'] ?? '');
        if ($expected === '') return;

        $h = (string)($cfg['auth']['header'] ?? 'X-Control-Plane-Token');
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $h));
        $got = (string)($_SERVER[$key] ?? '');

        if (!hash_equals($expected, $got)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok'=>false,'error'=>'unauthorized'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}
