<?php
namespace Core\Jobs;

interface QueueContract
{
    /** $options: queue, runAtUnix, maxAttempts, idempotencyKey, priority, visibilityTimeoutSec */
    public function enqueue(string $handler, array $payload = [], array $options = []): array;
    public function reserve(string $queue = 'default'): ?array;
    public function markDone(int $jobId): void;
    public function markFailed(int $jobId, string $error): void;
}
