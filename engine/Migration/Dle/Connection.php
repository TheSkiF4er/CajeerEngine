<?php
namespace Migration\Dle;

class Connection
{
    private static ?\PDO $pdo = null;

    public static function connect(array $cfg): \PDO
    {
        if (self::$pdo) return self::$pdo;

        $dsn = sprintf('%s:host=%s;dbname=%s;charset=%s',
            $cfg['driver'] ?? 'mysql',
            $cfg['host'] ?? '127.0.0.1',
            $cfg['database'] ?? '',
            $cfg['charset'] ?? 'utf8mb4'
        );

        self::$pdo = new \PDO($dsn, $cfg['username'] ?? 'root', $cfg['password'] ?? '', [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }
}
