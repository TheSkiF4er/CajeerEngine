<?php
namespace Core\Jobs;

use Database\DB;
use Observability\Logger;

class DBQueue implements QueueContract
{
    public function __construct(protected array $cfg = []) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql0 = ROOT_PATH . '/system/sql/platform_v3_0.sql';
        if (is_file($sql0)) $pdo->exec(file_get_contents($sql0));
        $sql = ROOT_PATH . '/system/sql/async_v3_1.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public function enqueue(string $handler, array $payload = [], array $options = []): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $queue = (string)($options['queue'] ?? ($this->cfg['default_queue'] ?? 'default'));
        $maxAttempts = (int)($options['maxAttempts'] ?? ($this->cfg['max_attempts'] ?? 10));
        $runAtUnix = $options['runAtUnix'] ?? null;
        $availableAt = $runAtUnix ? date('Y-m-d H:i:s', (int)$runAtUnix) : date('Y-m-d H:i:s');
        $visibility = (int)($options['visibilityTimeoutSec'] ?? ($this->cfg['visibility_timeout_sec'] ?? 60));
        $priority = (int)($options['priority'] ?? 0);
        $idem = $options['idempotencyKey'] ?? null;

        if ($idem) {
            $st0 = $pdo->prepare("SELECT id,status FROM ce_jobs WHERE tenant_id=:t AND idempotency_key=:k AND queue=:q ORDER BY id DESC LIMIT 1");
            $st0->execute([':t'=>$tenantId,':k'=>$idem,':q'=>$queue]);
            $ex = $st0->fetch(\PDO::FETCH_ASSOC);
            if ($ex) return ['ok'=>true,'job_id'=>(int)$ex['id'],'idempotent'=>true,'status'=>$ex['status']];
        }

        $st = $pdo->prepare("INSERT INTO ce_jobs(tenant_id,queue,handler,payload_json,attempts,max_attempts,status,run_at,available_at,visibility_timeout_sec,priority,idempotency_key,created_at,updated_at)
                             VALUES(:t,:q,:h,:p,0,:m,'queued',NULL,:aa,:vt,:pr,:ik,NOW(),NOW())");
        $st->execute([
            ':t'=>$tenantId, ':q'=>$queue, ':h'=>$handler,
            ':p'=>json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ':m'=>$maxAttempts, ':aa'=>$availableAt, ':vt'=>$visibility, ':pr'=>$priority, ':ik'=>$idem
        ]);
        $id = (int)$pdo->lastInsertId();
        Logger::info('jobs.enqueue', ['id'=>$id,'queue'=>$queue,'handler'=>$handler,'priority'=>$priority]);
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
                              AND (available_at IS NULL OR available_at <= NOW())
                            ORDER BY priority DESC, id ASC
                            LIMIT 1 FOR UPDATE")->fetch(\PDO::FETCH_ASSOC);

        if (!$row) { $pdo->commit(); return null; }

        $id = (int)$row['id'];
        $visibility = (int)($row['visibility_timeout_sec'] ?? 60);

        $pdo->prepare("UPDATE ce_jobs SET status='running', locked_at=NOW(), lock_token=:t,
                       attempts=attempts+1, updated_at=NOW(),
                       available_at = DATE_ADD(NOW(), INTERVAL ".$visibility." SECOND)
                       WHERE id=:id")->execute([':t'=>$token, ':id'=>$id]);

        $pdo->commit();

        $row['lock_token'] = $token;
        $row['payload'] = json_decode((string)($row['payload_json'] ?? '{}'), true) ?: [];
        return $row;
    }

    public function markDone(int $jobId): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_jobs SET status='done', lock_token=NULL, locked_at=NULL, updated_at=NOW() WHERE id=:id")
            ->execute([':id'=>$jobId]);
    }

    public function markFailed(int $jobId, string $error): void
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return;

        $job = $pdo->query("SELECT * FROM ce_jobs WHERE id=".(int)$jobId." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$job) return;

        $attempts = (int)($job['attempts'] ?? 0);
        $max = (int)($job['max_attempts'] ?? 10);

        if ($attempts < $max) {
            $base = min(60, 2 ** max(0, $attempts-1));
            $jitter = random_int(0, max(1, (int)($base/3)));
            $delay = min(300, $base + $jitter);
            $pdo->prepare("UPDATE ce_jobs SET status='queued', last_error=:e, lock_token=NULL, locked_at=NULL,
                           available_at=DATE_ADD(NOW(), INTERVAL ".$delay." SECOND), updated_at=NOW()
                           WHERE id=:id")->execute([':e'=>$error, ':id'=>$jobId]);
            Logger::warn('jobs.retry', ['id'=>$jobId,'delay_sec'=>$delay,'attempts'=>$attempts,'max'=>$max]);
            return;
        }

        $pdo->prepare("INSERT INTO ce_job_failures(job_id,tenant_id,queue,handler,payload_json,attempts,last_error,failed_at)
                       VALUES(:jid,:t,:q,:h,:p,:a,:e,NOW())")
            ->execute([
                ':jid'=>$jobId, ':t'=>(int)($job['tenant_id'] ?? 0), ':q'=>(string)($job['queue'] ?? 'default'),
                ':h'=>(string)($job['handler'] ?? ''), ':p'=>(string)($job['payload_json'] ?? null),
                ':a'=>$attempts, ':e'=>$error
            ]);
        $pdo->prepare("UPDATE ce_jobs SET status='failed', last_error=:e, lock_token=NULL, locked_at=NULL, updated_at=NOW() WHERE id=:id")
            ->execute([':e'=>$error, ':id'=>$jobId]);
        Logger::warn('jobs.dlq', ['id'=>$jobId,'attempts'=>$attempts]);
    }
}
