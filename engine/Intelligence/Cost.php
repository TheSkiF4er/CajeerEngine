<?php
namespace Intelligence;

use Database\DB;

class Cost
{
    public function __construct(protected array $cfg = []) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function charge(string $category, int $amount, array $meta = []): void
    {
        if (!($this->cfg['cost']['enabled'] ?? true)) return;
        $pdo = DB::pdo(); if(!$pdo) return;
        $this->ensureSchema();

        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $unit = (string)($this->cfg['cost']['unit'] ?? 'credits');

        $st = $pdo->prepare("INSERT INTO ce_cost_ledger(tenant_id,category,amount,unit,meta_json,ts)
                             VALUES(:t,:c,:a,:u,:m,:ts)");
        $st->execute([
            ':t'=>$tenantId, ':c'=>$category, ':a'=>$amount, ':u'=>$unit,
            ':m'=>json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ':ts'=>date('Y-m-d H:i:s'),
        ]);
    }
}
