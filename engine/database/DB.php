<?php
namespace Database;

class DB
{
    private static ?\PDO $pdo = null;

    public static function connect(array $cfg): \PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $driver = $cfg['driver'] ?? 'mysql';
        $host = $cfg['host'] ?? '127.0.0.1';
        $db = $cfg['database'] ?? '';
        $charset = $cfg['charset'] ?? 'utf8mb4';

        $dsn = sprintf('%s:host=%s;dbname=%s;charset=%s', $driver, $host, $db, $charset);
        self::$pdo = new \PDO($dsn, $cfg['username'] ?? 'root', $cfg['password'] ?? '', [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }

    public static function pdo(): ?\PDO
    {
        return self::$pdo;
    }
}
