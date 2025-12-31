<?php
namespace AIAssist;

use Database\DB;

class Recommendations
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/ai_v3_7.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function add(int $tenantId, string $kind, string $title, array $details = [], string $source = 'heuristic'): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        self::ensureSchema();
        $st = $pdo->prepare("INSERT INTO ce_ai_recommendations(tenant_id,kind,title,details_json,source,status,created_at,updated_at)
                             VALUES(:t,:k,:ti,:d,:s,'open',NOW(),NOW())");
        $st->execute([
          ':t'=>$tenantId,':k'=>$kind,':ti'=>$title,
          ':d'=>json_encode($details, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
          ':s'=>$source
        ]);
    }

    public static function list(int $tenantId, string $status = 'open', int $limit = 50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));
        $st = $pdo->prepare("SELECT id,kind,title,source,status,created_at FROM ce_ai_recommendations WHERE tenant_id=:t AND status=:s ORDER BY id DESC LIMIT $limit");
        $st->execute([':t'=>$tenantId,':s'=>$status]);
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
