<?php
namespace Observability;

use Database\DB;

class Health
{
    public static function live(): array
    {
        return ['ok'=>true,'time'=>date('c'),'version'=>trim((string)@file_get_contents(ROOT_PATH.'/system/version.txt'))];
    }

    public static function ready(): array
    {
        $pdo = DB::pdo();
        $dbOk = false;
        try { if ($pdo) { $pdo->query('SELECT 1'); $dbOk = true; } } catch (\Throwable $e) { $dbOk=false; }
        return ['ok'=>$dbOk,'db'=>$dbOk,'time'=>date('c')];
    }
}
