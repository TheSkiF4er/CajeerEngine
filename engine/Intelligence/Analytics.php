<?php
namespace Intelligence;

use Database\DB;

class Analytics
{
    public function __construct(protected array $cfg = []) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function track(string $eventType, array $meta = [], int $value = 1, ?int $userId = null, ?string $path = null, ?string $feature = null): void
    {
        if (!($this->cfg['analytics']['enabled'] ?? true)) return;
        $sr = (float)($this->cfg['analytics']['sample_rate'] ?? 1.0);
        if ($sr < 1.0 && mt_rand()/mt_getrandmax() > $sr) return;

        $pdo = DB::pdo(); if(!$pdo) return;
        $this->ensureSchema();

        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $st = $pdo->prepare("INSERT INTO ce_usage_events(tenant_id,user_id,event_type,path,feature,value,meta_json,ts)
                             VALUES(:t,:u,:e,:p,:f,:v,:m,:ts)");
        $st->execute([
            ':t'=>$tenantId,
            ':u'=>$userId,
            ':e'=>$eventType,
            ':p'=>$path,
            ':f'=>$feature,
            ':v'=>$value,
            ':m'=>json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ':ts'=>date('Y-m-d H:i:s'),
        ]);
    }
}
