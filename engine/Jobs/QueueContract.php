<?php
namespace Core\Jobs;
interface QueueContract
{
    public function enqueue(string $handler, array $payload = [], string $queue = 'default', ?int $runAtUnix = null, int $maxAttempts = 10): array;
    public function reserve(string $queue = 'default'): ?array;
    public function markDone(int $jobId): void;
    public function markFailed(int $jobId, string $error): void;
}
