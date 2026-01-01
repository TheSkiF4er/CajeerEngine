<?php
namespace Security;

class CSRF
{
    public static function token(array $cfg): string
    {
        $name = (string)($cfg['cookie_name'] ?? 'ce_csrf');
        $ttl  = (int)($cfg['token_ttl'] ?? 7200);

        if (!isset($_COOKIE[$name]) || !is_string($_COOKIE[$name]) || strlen($_COOKIE[$name]) < 16) {
            $t = bin2hex(random_bytes(32));
            setcookie($name, $t, [
                'expires' => time() + $ttl,
                'path' => '/',
                'httponly' => false, // needs to be readable by JS SPA
                'samesite' => 'Lax',
                'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            ]);
            $_COOKIE[$name] = $t;
        }
        return (string)$_COOKIE[$name];
    }

    public static function validate(array $cfg): bool
    {
        if (empty($cfg['enabled'])) return true;

        $cookie = (string)($cfg['cookie_name'] ?? 'ce_csrf');
        $header = (string)($cfg['header_name'] ?? 'X-CSRF-Token');
        $field  = (string)($cfg['field_name'] ?? '_csrf');

        $expected = (string)($_COOKIE[$cookie] ?? '');
        if ($expected === '') return false;

        $provided = '';
        if (!empty($_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $header))])) {
            $provided = (string)$_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $header))];
        } elseif (isset($_POST[$field])) {
            $provided = (string)$_POST[$field];
        }

        if ($provided === '') return false;
        return hash_equals($expected, $provided);
    }
}
