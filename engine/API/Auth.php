<?php
namespace API;

class Auth
{
    public static function token(): ?string
    {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $hdr, $m)) return trim($m[1]);
        if (isset($_GET['access_token'])) return (string)$_GET['access_token'];
        return null;
    }

    public static function check(string $scope='read'): bool
    {
        $cfg = is_file(ROOT_PATH . '/system/api.php') ? (array)require ROOT_PATH . '/system/api.php' : ['enabled'=>false];
        if (!($cfg['enabled'] ?? false)) return false;

        $t = self::token();
        if (!$t) return false;
        $tokens = (array)($cfg['tokens'] ?? []);
        if (!isset($tokens[$t])) return false;

        $scopes = (array)($tokens[$t]['scopes'] ?? []);
        return in_array($scope, $scopes, true) || in_array('admin', $scopes, true);
    }

    public static function require(string $scope='read'): void
    {
        if (!self::check($scope)) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['ok'=>false,'error'=>'Unauthorized'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
