<?php
namespace AutoUpdate;

use Database\DB;

class Rollout
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/platform_v2_5.sql'));
    }

    public static function create(string $version, string $channel, string $strategy, array $tenantIds): int
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return 0;
        $pdo->prepare("INSERT INTO ce_rollouts(version,channel,strategy,status,created_at,updated_at) VALUES(:v,:c,:s,'running',NOW(),NOW())")
            ->execute([':v'=>$version, ':c'=>$channel, ':s'=>$strategy]);
        $id = (int)$pdo->lastInsertId();
        $st = $pdo->prepare("INSERT INTO ce_rollout_targets(rollout_id,tenant_id,status,updated_at) VALUES(:r,:t,'pending',NOW())");
        for ($i=0;$i<count($tenantIds);$i++){
            $st->execute([':r'=>$id, ':t'=>(int)$tenantIds[$i]]);
        }
        return $id;
    }

    public static function nextBatch(int $rolloutId, int $batchSize): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        $st = $pdo->prepare("SELECT tenant_id FROM ce_rollout_targets WHERE rollout_id=:r AND status='pending' ORDER BY tenant_id ASC LIMIT :n");
        $st->bindValue(':r', $rolloutId, \PDO::PARAM_INT);
        $st->bindValue(':n', $batchSize, \PDO::PARAM_INT);
        $st->execute();
        return array_map(fn($x)=>(int)$x['tenant_id'], $st->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function mark(int $rolloutId, int $tenantId, string $status, string $err=''): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_rollout_targets SET status=:s,last_error=:e,updated_at=NOW() WHERE rollout_id=:r AND tenant_id=:t")
            ->execute([':s'=>$status, ':e'=>$err, ':r'=>$rolloutId, ':t'=>$tenantId]);
    }
}
