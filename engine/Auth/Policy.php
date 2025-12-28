<?php
namespace Auth;

class Policy
{
    public static function can(array $user, string $action, array $context = []): bool
    {
        if (($user['is_admin'] ?? false) === true) return true;

        $perms = (array)($user['permissions'] ?? []);
        if (in_array($action, $perms, true)) return true;

        $roles = (array)($user['roles'] ?? []);
        $map = self::rolePermissions();
        foreach ($roles as $r) {
            $rp = (array)($map[$r] ?? []);
            if (in_array('*', $rp, true) || in_array($action, $rp, true)) return true;
        }
        return false;
    }

    public static function rolePermissions(): array
    {
        $file = ROOT_PATH . '/system/permissions.php';
        return is_file($file) ? (array)require $file : [];
    }
}
