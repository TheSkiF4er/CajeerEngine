<?php
namespace Core\Jobs;

use Observability\Logger;

class Worker
{
    protected bool $running = true;

    public function __construct(protected QueueContract $q, protected array $cfg = [])
    {
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, fn() => $this->running = false);
            pcntl_signal(SIGINT, fn() => $this->running = false);
        }
    }

    public function work(string $queue = 'default', int $max = 0): void
    {
        $maxInflight = (int)($this->cfg['worker']['max_inflight'] ?? 50);
        $sleepMs = (int)($this->cfg['worker']['sleep_ms'] ?? 250);

        $processed = 0;

        while ($this->running) {
            if (function_exists('pcntl_signal_dispatch')) pcntl_signal_dispatch();

            $job = $this->q->reserve($queue);
            if (!$job) {
                usleep(max(10, $sleepMs) * 1000);
                if ($max > 0 && $processed >= $max) break;
                continue;
            }

            $id = (int)$job['id'];
            $handler = (string)$job['handler'];
            $payload = (array)($job['payload'] ?? []);

            try {
                Logger::info('jobs.run', ['id'=>$id,'handler'=>$handler]);
                $this->dispatch($handler, $payload);
                $this->q->markDone($id);
                $processed++;
            } catch (\Throwable $e) {
                $this->q->markFailed($id, $e->getMessage());
                Logger::warn('jobs.failed', ['id'=>$id,'err'=>$e->getMessage()]);
                $processed++;
            }

            if ($processed % max(1, $maxInflight) == 0) {
                usleep(max(10, $sleepMs) * 1000);
            }

            if ($max > 0 && $processed >= $max) break;
        }

        echo "Worker stopped. processed=$processed\n";
    }

    protected function dispatch(string $handler, array $payload): void
    {
        if (strpos($handler, '@') !== false) {
            [$class, $method] = explode('@', $handler, 2);
            $obj = new $class();
            $obj->{$method}($payload);
            return;
        }
        if (is_callable($handler)) {
            call_user_func($handler, $payload);
            return;
        }
        throw new \RuntimeException("Unknown job handler: $handler");
    }
}
