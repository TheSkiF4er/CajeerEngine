<?php
namespace Intelligence;

use Database\DB;

class Performance
{
    public function __construct(protected array $cfg = []) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function logRequest(string $method, string $path, int $status, int $durationMs, array $meta = []): void
    {
        if (!($this->cfg['performance']['enabled'] ?? true)) return;

        $slow = (int)($this->cfg['performance']['slow_request_ms'] ?? 500);
        if ($durationMs < $slow) return;

        $pdo = DB::pdo(); if(!$pdo) return;
        $this->ensureSchema();
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $st = $pdo->prepare("INSERT INTO ce_perf_requests(tenant_id,method,path,status,duration_ms,meta_json,ts)
                             VALUES(:t,:m,:p,:s,:d,:j,:ts)");
        $st->execute([
            ':t'=>$tenantId, ':m'=>$method, ':p'=>$path, ':s'=>$status, ':d'=>$durationMs,
            ':j'=>json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ':ts'=>date('Y-m-d H:i:s'),
        ]);
    }

    public function logQuery(string $sql, int $durationMs, array $meta = []): void
    {
        if (!($this->cfg['performance']['enabled'] ?? true)) return;

        $slow = (int)($this->cfg['performance']['slow_query_ms'] ?? 200);
        if ($durationMs < $slow) return;

        $pdo = DB::pdo(); if(!$pdo) return;
        $this->ensureSchema();
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $hash = hash('sha256', preg_replace('/\s+/', ' ', trim($sql)));

        $st = $pdo->prepare("INSERT INTO ce_perf_queries(tenant_id,query_hash,duration_ms,meta_json,ts)
                             VALUES(:t,:h,:d,:j,:ts)");
        $st->execute([
            ':t'=>$tenantId, ':h'=>$hash, ':d'=>$durationMs,
            ':j'=>json_encode(['sql'=>$sql] + $meta, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ':ts'=>date('Y-m-d H:i:s'),
        ]);
    }
}
