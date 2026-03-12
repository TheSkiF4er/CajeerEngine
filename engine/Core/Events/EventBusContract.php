<?php
namespace Core\Events;

if (!interface_exists(__NAMESPACE__ . '\\EventBusContract', false)) {
    interface EventBusContract
    {
        public function on(string $event, callable $listener, int $priority = 0): void;
        public function emit(string $event, array $payload = []): mixed;
    }
}
