<?php
namespace Intent;

use ControlPlane\PolicyManager;
use ControlPlane\FleetManager;
use ControlPlane\SelfHealing;
use EventMesh\Mesh;
use Database\DB;

class Reconciler
{
    public static function run(string $runKind='manual', int $limit = 25): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'no_db'];
        IntentStore::ensureSchema();
        $sql = ROOT_PATH . '/system/sql/platform_v4_0.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));

        $st = $pdo->prepare("SELECT * FROM ce_platform_intents WHERE status='pending' ORDER BY id ASC LIMIT :l");
        $st->bindValue(':l', max(1, min(200, $limit)), \PDO::PARAM_INT);
        $st->execute();
        $items = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $applied = 0; $failed = 0;
        foreach ($items as $it) {
            $id = (int)$it['id'];
            $tenant = (int)$it['tenant_id'];
            $kind = (string)$it['kind'];
            $desired = json_decode($it['desired_json'] ?? '{}', true) ?: [];

            try {
                if ($kind === 'PolicyIntent') {
                    PolicyManager::set('tenant', $tenant, 0, 'policies', $desired);
                } elseif ($kind === 'FleetIntent') {
                    FleetManager::register($desired + ['tenant_id'=>$tenant]);
                } elseif ($kind === 'SelfHealIntent') {
                    $k = (string)($desired['kind'] ?? 'flush_cache');
                    SelfHealing::enqueue($tenant, (int)($desired['site_id'] ?? 0), $k, (array)($desired['input'] ?? []));
                } elseif ($kind === 'EventIntent') {
                    Mesh::publish((string)($desired['topic'] ?? 'ce:intents'), (array)($desired['payload'] ?? $desired));
                } else {
                    throw new \RuntimeException('unknown_intent_kind');
                }

                IntentStore::mark($id, 'applied', '');
                $applied++;
            } catch (\Throwable $e) {
                IntentStore::mark($id, 'failed', $e->getMessage());
                $failed++;
            }
        }

        $stats = ['run_kind'=>$runKind,'seen'=>count($items),'applied'=>$applied,'failed'=>$failed];

        $pdo->prepare("INSERT INTO ce_platform_reconcile_runs(run_kind,stats_json,created_at) VALUES(:k,:s,NOW())")
            ->execute([':k'=>$runKind,':s'=>json_encode($stats, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);

        return ['ok'=>true,'stats'=>$stats];
    }
}
