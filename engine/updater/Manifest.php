<?php
namespace Updater;

class Manifest
{
    public function __construct(public array $data) {}

    public function channel(string $name): array
    {
        return (array)($this->data['channels'][$name] ?? []);
    }
}
