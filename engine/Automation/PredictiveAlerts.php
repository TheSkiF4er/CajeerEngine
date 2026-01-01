<?php
namespace Automation;

use Database\DB;

class PredictiveAlerts
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function create(string $severity, string $title, array $details = [], int $tenantId = 0): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        self::ensureSchema();

        $pdo->prepare("INSERT INTO ce_alerts(tenant_id,severity,title,details_json,status,created_at,updated_at)
                       VALUES(:t,:s,:ti,:d,'open',NOW(),NOW())")
            ->execute([
                ':t'=>$tenantId,':s'=>$severity,':ti'=>$title,
                ':d'=>json_encode($details, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);
    }
}
