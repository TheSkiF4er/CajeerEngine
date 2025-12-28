<?php
namespace Security;

use Database\Connection;

class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['auth']['user_id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['auth'] ?? null;
    }

    public static function login(string $username, string $password): bool
    {
        $pdo = Connection::pdo();
        $st = $pdo->prepare('SELECT u.id,u.username,u.password_hash,u.group_id,g.title as group_title FROM users u LEFT JOIN groups g ON g.id=u.group_id WHERE u.username=:u LIMIT 1');
        $st->execute(['u'=>$username]);
        $row = $st->fetch();
        if (!$row) return false;

        if (!password_verify($password, (string)$row['password_hash'])) return false;

        $_SESSION['auth'] = [
            'user_id' => (int)$row['id'],
            'username' => (string)$row['username'],
            'group_id' => (int)$row['group_id'],
            'group_title' => (string)($row['group_title'] ?? ''),
        ];
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['auth']);
    }
}
