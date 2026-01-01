<?php
namespace NoCode;

use Database\DB;

class Workflows
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/frontend_v3_4.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function run(string $slug, array $payload = [], int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $wf = $pdo->query("SELECT * FROM ce_workflows WHERE tenant_id=".(int)$tenantId." AND slug=".$pdo->quote($slug)." LIMIT 1")
                  ->fetch(\PDO::FETCH_ASSOC);
        if (!$wf) return ['ok'=>false,'error'=>'workflow_not_found'];

        // Foundation execution: returns graph + input for external executor or future engine
        return ['ok'=>true,'mode'=>'foundation','workflow'=>['slug'=>$slug,'graph_json'=>$wf['graph_json']],'input'=>$payload];
    }
}
