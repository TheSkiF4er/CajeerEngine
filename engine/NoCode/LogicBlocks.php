<?php
namespace NoCode;

use Database\DB;

class LogicBlocks
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/frontend_v3_4.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function list(int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $items = $pdo->query("SELECT slug,version,definition_json FROM ce_logic_blocks WHERE tenant_id=".(int)$tenantId." ORDER BY slug,version DESC LIMIT 500")
                     ->fetchAll(\PDO::FETCH_ASSOC);
        return ['ok'=>true,'items'=>$items];
    }
}
