<?php
namespace Core\Events;

class EventBus
{
    private array $listeners = [];

    public function on(string $event, callable $handler, int $priority = 0): void
    {
        $this->listeners[$event][] = ['priority'=>$priority, 'handler'=>$handler];
        usort($this->listeners[$event], fn($a,$b)=>$b['priority'] <=> $a['priority']);
    }

    public function emit(string $name, array $payload = []): Event
    {
        $ev = new Event($name, $payload, false);
        foreach ($this->listenersFor($name) as $l) {
            ($l['handler'])($ev);
            if ($ev->stopped) break;
        }
        return $ev;
    }

    public function filter(string $name, mixed $value, array $payload = []): mixed
    {
        $ev = new Event($name, array_merge($payload, ['value'=>$value]), false);
        foreach ($this->listenersFor($name) as $l) {
            ($l['handler'])($ev);
            if ($ev->stopped) break;
        }
        return $ev->payload['value'] ?? $value;
    }

    private function listenersFor(string $name): array
    {
        $out = $this->listeners[$name] ?? [];
        foreach ($this->listeners as $k=>$list) {
            if (str_contains($k, '*')) {
                $prefix = rtrim($k, '*');
                if ($prefix !== '' && str_starts_with($name, $prefix)) {
                    foreach ($list as $l) $out[] = $l;
                }
            }
        }
        usort($out, fn($a,$b)=>$b['priority'] <=> $a['priority']);
        return $out;
    }
}
