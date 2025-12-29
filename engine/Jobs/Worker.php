<?php
namespace Core\Jobs;
use Observability\Logger;

class Worker
{
    public function __construct(protected QueueContract $q) {}
    public function work(string $queue = 'default', int $max = 50): void
    {
        $count = 0;
        while ($count < $max) {
            $job = $this->q->reserve($queue);
            if (!$job) { echo "No jobs\n"; return; }

            $id = (int)$job['id'];
            $handler = (string)$job['handler'];
            $payload = (array)($job['payload'] ?? []);

            try {
                Logger::info('jobs.run', ['id'=>$id,'handler'=>$handler]);
                $this->dispatch($handler, $payload);
                $this->q->markDone($id);
                $count++;
            } catch (\Throwable $e) {
                $this->q->markFailed($id, $e->getMessage());
                Logger::warn('jobs.failed', ['id'=>$id,'err'=>$e->getMessage()]);
                $count++;
            }
        }
    }
    protected function dispatch(string $handler, array $payload): void
    {
        if (strpos($handler, '@') !== false) {
            [$class, $method] = explode('@', $handler, 2);
            $obj = new $class();
            $obj->{$method}($payload);
            return;
        }
        if (is_callable($handler)) { call_user_func($handler, $payload); return; }
        throw new \RuntimeException("Unknown job handler: $handler");
    }
}
