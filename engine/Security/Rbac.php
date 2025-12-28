<?php
namespace Security;

use Database\Connection;

class Rbac
{
    public static function allow(string $permission): bool
    {
        $u = Auth::user();
        if (!$u) return false;

        // group 1 = superadmin shortcut
        if ((int)$u['group_id'] === 1) return true;

        $pdo = Connection::pdo();
        $st = $pdo->prepare('SELECT 1 FROM group_permissions WHERE group_id=:g AND permission=:p AND allowed=1 LIMIT 1');
        $st->execute(['g'=>(int)$u['group_id'], 'p'=>$permission]);
        return (bool)$st->fetch();
    }
}
