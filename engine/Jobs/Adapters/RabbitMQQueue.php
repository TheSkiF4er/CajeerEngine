<?php
namespace Core\Jobs\Adapters;

use Core\Jobs\QueueContract;
use Observability\Logger;

class RabbitMQQueue implements QueueContract
{
    public function __construct(protected array $cfg = []) {}
    public function enqueue(string $handler, array $payload = [], array $options = []): array
    {
        Logger::info('jobs.rabbitmq.enqueue', ['handler'=>$handler,'note'=>'stub']);
        return ['ok'=>false,'error'=>'not_implemented'];
    }
    public function reserve(string $queue = 'default'): ?array { return null; }
    public function markDone(int $jobId): void {}
    public function markFailed(int $jobId, string $error): void {}
}
