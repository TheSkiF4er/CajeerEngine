<?php
namespace AIAssist;

use Database\DB;

class Governance
{
    public function __construct(protected array $cfg = []) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/ai_v3_7.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function policy(int $tenantId): array
    {
        $default = $this->cfg['governance'] ?? [];
        $pdo = DB::pdo(); if(!$pdo) return $default;
        $this->ensureSchema();

        $st = $pdo->prepare("SELECT * FROM ce_ai_policies WHERE tenant_id=:t LIMIT 1");
        $st->execute([':t'=>$tenantId]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            $optIn = (int)($default['default_opt_in'] ?? 0);
            $allow = $default['data_boundaries']['allow'] ?? [];
            $store = (int)($default['store_requests'] ?? 1);
            $transp = (int)($default['prompt_transparency'] ?? 1);

            $pdo->prepare("INSERT INTO ce_ai_policies(tenant_id,opt_in,allow_content,allow_templates,allow_logs,allow_secrets,allow_pii,store_requests,transparency,created_at,updated_at)
                           VALUES(:t,:o,:c,:tpl,:l,:s,:p,:sr,:tr,NOW(),NOW())")
                ->execute([
                    ':t'=>$tenantId,':o'=>$optIn,
                    ':c'=>(int)($allow['content'] ?? 1),
                    ':tpl'=>(int)($allow['templates'] ?? 1),
                    ':l'=>(int)($allow['logs'] ?? 0),
                    ':s'=>(int)($allow['secrets'] ?? 0),
                    ':p'=>(int)($allow['pii'] ?? 0),
                    ':sr'=>$store,
                    ':tr'=>$transp,
                ]);

            $st->execute([':t'=>$tenantId]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
        }

        return [
          'opt_in' => (int)($row['opt_in'] ?? 0) === 1,
          'allow' => [
            'content' => (int)($row['allow_content'] ?? 1) === 1,
            'templates' => (int)($row['allow_templates'] ?? 1) === 1,
            'logs' => (int)($row['allow_logs'] ?? 0) === 1,
            'secrets' => (int)($row['allow_secrets'] ?? 0) === 1,
            'pii' => (int)($row['allow_pii'] ?? 0) === 1,
          ],
          'store_requests' => (int)($row['store_requests'] ?? 1) === 1,
          'transparency' => (int)($row['transparency'] ?? 1) === 1,
        ];
    }

    public function setOptIn(int $tenantId, bool $optIn): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $this->ensureSchema();
        $pdo->prepare("INSERT INTO ce_ai_policies(tenant_id,opt_in,created_at,updated_at)
                       VALUES(:t,:o,NOW(),NOW())
                       ON DUPLICATE KEY UPDATE opt_in=:o2, updated_at=NOW()")
            ->execute([':t'=>$tenantId,':o'=>$optIn?1:0,':o2'=>$optIn?1:0]);
    }
}
