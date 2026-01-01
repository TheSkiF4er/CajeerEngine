<?php
namespace Core\Events;

use Core\Jobs\QueueContract;
use Observability\Logger;

class AsyncEventBus implements EventBusContract
{
    public function __construct(
        protected EventBusContract $syncBus,
        protected QueueContract $jobs,
        protected bool $persist = true
    ) {}

    public function on(string $event, callable $listener, int $priority = 0): void
    {
        $this->syncBus->on($event, $listener, $priority);
    }

    public function emit(string $event, array $payload = []): void
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $eventId = null;
        if ($this->persist) {
            $res = EventStore::store($event, $payload, $tenantId);
            if (!empty($res['event_id'])) $eventId = (int)$res['event_id'];
        }

        $this->jobs->enqueue('Core\\Events\\AsyncEventWorker@handle', [
            'event' => $event,
            'payload' => $payload,
            'event_id' => $eventId,
        ], [
            'queue' => 'events',
            'priority' => 10,
            'idempotencyKey' => $eventId ? ('event:' . $eventId) : null,
        ]);

        Logger::info('events.emit_async', ['event'=>$event,'event_id'=>$eventId]);
    }
}
