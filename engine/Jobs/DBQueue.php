<?php
namespace Core\Jobs;
use Database\DB;
use Observability\Logger;

class DBQueue implements QueueContract
{
    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/platform_v3_0.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function enqueue(string $handler, array $payload = [], string $queue = 'default', ?int $runAtUnix = null, int $maxAttempts = 10): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $runAt = $runAtUnix ? date('Y-m-d H:i:s', $runAtUnix) : null;

        $st = $pdo->prepare("INSERT INTO ce_jobs(tenant_id,queue,handler,payload_json,attempts,max_attempts,status,run_at,created_at,updated_at)
                             VALUES(:t,:q,:h,:p,0,:m,'queued',:ra,NOW(),NOW())");
        $st->execute([
            ':t'=>$tenantId, ':q'=>$queue, ':h'=>$handler,
            ':p'=>json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ':m'=>$maxAttempts, ':ra'=>$runAt
        ]);
        $id = (int)$pdo->lastInsertId();
        Logger::info('jobs.enqueue', ['id'=>$id,'queue'=>$queue,'handler'=>$handler]);
        return ['ok'=>true,'job_id'=>$id];
    }

    public function reserve(string $queue = 'default'): ?array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return null;

        $token = bin2hex(random_bytes(16));
        $pdo->beginTransaction();

        $row = $pdo->query("SELECT * FROM ce_jobs
                            WHERE status='queued' AND queue=".$pdo->quote($queue)."
                              AND (run_at IS NULL OR run_at <= NOW())
                            ORDER BY id ASC LIMIT 1 FOR UPDATE")->fetch(\PDO::FETCH_ASSOC);
        if (!$row) { $pdo->commit(); return null; }

        $id = (int)$row['id'];
        $pdo->prepare("UPDATE ce_jobs SET status='running', locked_at=NOW(), lock_token=:t, attempts=attempts+1, updated_at=NOW() WHERE id=:id")
            ->execute([':t'=>$token, ':id'=>$id]);

        $pdo->commit();
        $row['lock_token'] = $token;
        $row['payload'] = json_decode((string)($row['payload_json'] ?? '{}'), true) ?: [];
        return $row;
    }

    public function markDone(int $jobId): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_jobs SET status='done', updated_at=NOW() WHERE id=:id")->execute([':id'=>$jobId]);
    }

    public function markFailed(int $jobId, string $error): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_jobs SET status='failed', last_error=:e, updated_at=NOW() WHERE id=:id")->execute([':e'=>$error, ':id'=>$jobId]);
    }
}
