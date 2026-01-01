<?php
namespace AIAssist;

use Database\DB;

class PromptStore
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/ai_v3_7.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function save(array $row): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        self::ensureSchema();

        $st = $pdo->prepare("INSERT INTO ce_ai_requests(tenant_id,user_id,provider,model,purpose,prompt_json,response_json,tokens_in,tokens_out,latency_ms,status,reason,created_at)
                             VALUES(:t,:u,:p,:m,:pur,:pj,:rj,:ti,:to,:lm,:st,:rs,NOW())");
        $st->execute([
          ':t'=>(int)($row['tenant_id'] ?? 0),
          ':u'=>$row['user_id'] ?? null,
          ':p'=>(string)($row['provider'] ?? ''),
          ':m'=>$row['model'] ?? null,
          ':pur'=>(string)($row['purpose'] ?? 'admin'),
          ':pj'=>json_encode($row['prompt'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
          ':rj'=>json_encode($row['response'] ?? null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
          ':ti'=>$row['tokens_in'] ?? null,
          ':to'=>$row['tokens_out'] ?? null,
          ':lm'=>$row['latency_ms'] ?? null,
          ':st'=>(string)($row['status'] ?? 'ok'),
          ':rs'=>$row['reason'] ?? null,
        ]);
    }

    public static function list(int $tenantId, string $purpose = '', int $limit = 50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));

        if ($purpose !== '') {
            $st = $pdo->prepare("SELECT id,purpose,provider,model,status,reason,created_at FROM ce_ai_requests WHERE tenant_id=:t AND purpose=:p ORDER BY id DESC LIMIT $limit");
            $st->execute([':t'=>$tenantId,':p'=>$purpose]);
        } else {
            $st = $pdo->prepare("SELECT id,purpose,provider,model,status,reason,created_at FROM ce_ai_requests WHERE tenant_id=:t ORDER BY id DESC LIMIT $limit");
            $st->execute([':t'=>$tenantId]);
        }
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function get(int $tenantId, int $id): ?array
    {
        $pdo = DB::pdo(); if(!$pdo) return null;
        self::ensureSchema();
        $st = $pdo->prepare("SELECT * FROM ce_ai_requests WHERE tenant_id=:t AND id=:id LIMIT 1");
        $st->execute([':t'=>$tenantId,':id'=>$id]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
