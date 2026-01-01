<?php
namespace Database;

use Core\Config;

class Connection
{
    public static function pdo(): \PDO
    {
        $db = require ROOT_PATH . '/system/db.php';
        return DB::connect($db);
    }
}
