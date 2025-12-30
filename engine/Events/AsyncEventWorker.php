<?php
namespace Core\Events;

use Observability\Logger;

class AsyncEventWorker
{
    public function handle(array $job): void
    {
        $event = (string)($job['event'] ?? '');
        $payload = (array)($job['payload'] ?? []);
        $eventId = $job['event_id'] ?? null;

        if (isset($GLOBALS['CE_KERNEL'])) {
            $sync = $GLOBALS['CE_KERNEL']->get('events_sync');
            if ($sync) $sync->emit($event, $payload);
        }

        if ($eventId) EventStore::markProcessed((int)$eventId);
        Logger::info('events.delivered', ['event'=>$event,'event_id'=>$eventId]);
    }
}
