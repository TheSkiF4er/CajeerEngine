<?php
namespace ControlPlane;

use Database\DB;

class RolloutManager
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/control_plane_v3_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function plan(string $scope, int $tenantId, int $siteId, string $targetVersion, array $policy): array
    {
        $strategy = (string)($policy['rollouts']['default_strategy'] ?? 'canary');
        $step = (int)($policy['rollouts']['max_percent_per_step'] ?? 20);
        $delay = (int)($policy['rollouts']['step_delay_sec'] ?? 300);

        return [
          'scope'=>$scope,
          'tenant_id'=>$tenantId,
          'site_id'=>$siteId,
          'target_version'=>$targetVersion,
          'strategy'=>$strategy,
          'step_percent'=>max(1, min(100, $step)),
          'step_delay_sec'=>max(0, $delay),
          'health_gate'=>(bool)($policy['rollouts']['health_gate'] ?? true),
        ];
    }

    public static function create(array $plan): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'no_db'];
        self::ensureSchema();

        $pdo->prepare("INSERT INTO ce_rollouts(scope,tenant_id,site_id,target_version,strategy,step_percent,step_delay_sec,status,current_percent,details_json,created_at,updated_at)
                       VALUES(:sc,:t,:s,:v,:st,:sp,:sd,'planned',0,:d,NOW(),NOW())")
            ->execute([
              ':sc'=>$plan['scope'], ':t'=>$plan['tenant_id'], ':s'=>$plan['site_id'],
              ':v'=>$plan['target_version'], ':st'=>$plan['strategy'],
              ':sp'=>$plan['step_percent'], ':sd'=>$plan['step_delay_sec'],
              ':d'=>json_encode($plan, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);

        $id = (int)$pdo->lastInsertId();
        return ['ok'=>true,'rollout_id'=>$id,'plan'=>$plan];
    }

    public static function list(string $status=''): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        if ($status !== '') {
            $st = $pdo->prepare("SELECT * FROM ce_rollouts WHERE status=:s ORDER BY id DESC LIMIT 200");
            $st->execute([':s'=>$status]);
        } else {
            $st = $pdo->query("SELECT * FROM ce_rollouts ORDER BY id DESC LIMIT 200");
        }
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function step(int $id, int $healthScore = 100): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'no_db'];
        self::ensureSchema();

        $st = $pdo->prepare("SELECT * FROM ce_rollouts WHERE id=:id LIMIT 1");
        $st->execute([':id'=>$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return ['ok'=>false,'error'=>'not_found'];

        $plan = json_decode($r['details_json'] ?? '{}', true) ?: [];
        $healthGate = (bool)($plan['health_gate'] ?? true);

        if ($healthGate && $healthScore < 70) {
            $pdo->prepare("UPDATE ce_rollouts SET status='paused', updated_at=NOW() WHERE id=:id")->execute([':id'=>$id]);
            return ['ok'=>false,'status'=>'paused','reason'=>'health_gate','health'=>$healthScore];
        }

        $cur = (int)($r['current_percent'] ?? 0);
        $step = (int)($r['step_percent'] ?? 20);
        $next = min(100, $cur + max(1,$step));

        $status = $next >= 100 ? 'completed' : 'running';
        $pdo->prepare("UPDATE ce_rollouts SET status=:st,current_percent=:cp,updated_at=NOW() WHERE id=:id")
            ->execute([':st'=>$status,':cp'=>$next,':id'=>$id]);

        return ['ok'=>true,'status'=>$status,'current_percent'=>$next];
    }
}
