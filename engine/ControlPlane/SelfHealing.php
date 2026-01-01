<?php
namespace ControlPlane;

use Database\DB;

class SelfHealing
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/control_plane_v3_9.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function enqueue(int $tenantId, int $siteId, string $kind, array $input = []): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'no_db'];
        self::ensureSchema();

        $pdo->prepare("INSERT INTO ce_self_heal_actions(tenant_id,site_id,kind,input_json,status,result_json,created_at,updated_at)
                       VALUES(:t,:s,:k,:i,'queued',NULL,NOW(),NOW())")
            ->execute([
              ':t'=>$tenantId,':s'=>$siteId,':k'=>$kind,
              ':i'=>json_encode($input, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
            ]);
        return ['ok'=>true,'action_id'=>(int)$pdo->lastInsertId()];
    }

    public static function runOne(): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'no_db'];
        self::ensureSchema();

        $st = $pdo->query("SELECT * FROM ce_self_heal_actions WHERE status='queued' ORDER BY id ASC LIMIT 1");
        $a = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$a) return ['ok'=>true,'status'=>'idle'];

        $id = (int)$a['id'];
        $pdo->prepare("UPDATE ce_self_heal_actions SET status='running', updated_at=NOW() WHERE id=:id")->execute([':id'=>$id]);

        $kind = (string)$a['kind'];
        $input = json_decode($a['input_json'] ?? '{}', true) ?: [];
        $result = ['kind'=>$kind,'input'=>$input,'note'=>'foundation'];

        // foundation: these actions are stubs; integrate with real supervisors/ops agents later
        if ($kind === 'flush_cache') {
            $result['status'] = 'cache_flush_requested';
        } elseif ($kind === 'restart_workers') {
            $result['status'] = 'worker_restart_requested';
        } else {
            $result['status'] = 'noop';
        }

        $pdo->prepare("UPDATE ce_self_heal_actions SET status='done', result_json=:r, updated_at=NOW() WHERE id=:id")
            ->execute([':r'=>json_encode($result, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), ':id'=>$id]);

        return ['ok'=>true,'status'=>'done','action_id'=>$id,'result'=>$result];
    }
}
