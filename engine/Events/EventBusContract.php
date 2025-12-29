<?php
namespace Core\Events;
interface EventBusContract
{
    public function on(string $event, callable $listener, int $priority = 0): void;
    public function emit(string $event, array $payload = []): void;
}
