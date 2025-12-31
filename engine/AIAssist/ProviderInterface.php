<?php
namespace AIAssist;

interface ProviderInterface
{
    public function id(): string;
    public function chat(array $payload): array;
}
