<?php
namespace AB;

use Database\DB;

class ABTesting
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/frontend_v3_4.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function assign(string $experimentKey, string $userHash, int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $row = $pdo->query("SELECT * FROM ce_ab_assignments WHERE tenant_id=".(int)$tenantId." AND experiment_key=".$pdo->quote($experimentKey)." AND user_hash=".$pdo->quote($userHash)." LIMIT 1")
                   ->fetch(\PDO::FETCH_ASSOC);
        if ($row) return ['ok'=>true,'variant'=>$row['variant'],'cached'=>true];

        $exp = $pdo->query("SELECT * FROM ce_ab_experiments WHERE tenant_id=".(int)$tenantId." AND key_name=".$pdo->quote($experimentKey)." LIMIT 1")
                   ->fetch(\PDO::FETCH_ASSOC);
        if (!$exp) return ['ok'=>false,'error'=>'experiment_not_found'];

        $variants = json_decode((string)($exp['variants_json'] ?? '{}'), true) ?: [];
        $keys = array_keys($variants ?: []);
        if (!$keys) return ['ok'=>false,'error'=>'no_variants'];

        // deterministic pick using hash
        $idx = hexdec(substr(hash('sha256', $experimentKey.'|'.$userHash), 0, 8)) % count($keys);
        $variant = (string)$keys[$idx];

        $pdo->prepare("INSERT INTO ce_ab_assignments(tenant_id,experiment_key,user_hash,variant,assigned_at)
                       VALUES(:t,:k,:u,:v,NOW())")
            ->execute([':t'=>$tenantId,':k'=>$experimentKey,':u'=>$userHash,':v'=>$variant]);

        return ['ok'=>true,'variant'=>$variant,'cached'=>false];
    }
}
