<?php
namespace Automation;

use Database\DB;
use Intelligence\Analytics;
use Intelligence\Performance;
use Intelligence\Cost;

class PolicyEngine
{
    public function __construct(protected array $cfg = []) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function upsertPolicies(array $policies, int $tenantId = 0): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $this->ensureSchema();

        foreach ($policies as $p) {
            $id = (string)($p['id'] ?? '');
            $type = (string)($p['type'] ?? '');
            if ($id==='' || $type==='') continue;

            $pdo->prepare("INSERT INTO ce_auto_policies(tenant_id,policy_id,type,spec_json,enabled,created_at,updated_at)
                           VALUES(:t,:id,:ty,:sp,1,NOW(),NOW())
                           ON DUPLICATE KEY UPDATE type=:ty2, spec_json=:sp2, updated_at=NOW()")
                ->execute([
                    ':t'=>$tenantId,':id'=>$id,':ty'=>$type,':sp'=>json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                    ':ty2'=>$type,':sp2'=>json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ]);
        }
    }

    public function runOnce(int $tenantId = 0): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
        $this->ensureSchema();

        $rows = $pdo->query("SELECT * FROM ce_auto_policies WHERE tenant_id=".(int)$tenantId." AND enabled=1 ORDER BY id ASC")
                    ->fetchAll(\PDO::FETCH_ASSOC);

        $decisions = [];
        foreach ($rows as $r) {
            $spec = json_decode((string)$r['spec_json'], true) ?: [];
            $policyId = (string)$r['policy_id'];
            $type = (string)$r['type'];

            // Foundation evaluation: we only record policy spec; metrics adapters in 3.5.x
            $decision = ['policy_id'=>$policyId,'type'=>$type,'status'=>'skipped','reason'=>'metrics_adapter_not_implemented'];

            $pdo->prepare("INSERT INTO ce_auto_runs(tenant_id,policy_id,status,decision_json,created_at)
                           VALUES(:t,:p,:s,:d,NOW())")
                ->execute([
                    ':t'=>$tenantId,':p'=>$policyId,':s'=>$decision['status'],
                    ':d'=>json_encode($decision, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ]);

            $decisions[] = $decision;
        }

        return ['ok'=>true,'decisions'=>$decisions];
    }
}
