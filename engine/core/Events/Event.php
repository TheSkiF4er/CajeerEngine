<?php
namespace Core\Events;

class Event
{
    public function __construct(
        public string $name,
        public array $payload = [],
        public bool $stopped = false
    ) {}

    public function stop(): void { $this->stopped = true; }
}
