<?php
namespace Core\Events;
use Observability\Logger;

class EventBus implements EventBusContract
{
    protected array $listeners = [];
    public function on(string $event, callable $listener, int $priority = 0): void
    {
        $this->listeners[$event][] = ['p'=>$priority,'l'=>$listener];
        usort($this->listeners[$event], fn($a,$b) => $b['p'] <=> $a['p']);
    }
    public function emit(string $event, array $payload = []): void
    {
        Logger::info('events.emit', ['event'=>$event]);
        foreach (($this->listeners[$event] ?? []) as $it) {
            try { ($it['l'])($payload); }
            catch (\Throwable $e) { Logger::warn('events.listener_error', ['event'=>$event,'err'=>$e->getMessage()]); }
        }
        foreach (($this->listeners['*'] ?? []) as $it) {
            try { ($it['l'])(['event'=>$event,'payload'=>$payload]); } catch (\Throwable $e) {}
        }
    }
}
