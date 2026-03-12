<?php
namespace Jobs;

class QueueFactory
{
    public static function make(string $driver = 'db', array $cfg = []): QueueContract
    {
        $cfg['driver'] = $driver;
        return self::fromConfig($cfg);
    }

    public static function fromConfig(array $cfg): QueueContract
    {
        $driver = strtolower((string)($cfg['driver'] ?? 'db'));
        return match ($driver) {
            'redis' => new RedisQueue($cfg),
            'sqs' => new Adapters\SQSQueue($cfg),
            'rabbitmq' => new Adapters\RabbitMQQueue($cfg),
            default => new DBQueue($cfg),
        };
    }
}
