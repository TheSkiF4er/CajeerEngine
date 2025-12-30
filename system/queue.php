<?php
return [
  'driver' => 'db',
  'default_queue' => 'default',
  'visibility_timeout_sec' => 60,
  'max_attempts' => 10,

  'worker' => [
    'max_inflight' => 50,
    'sleep_ms' => 250,
  ],

  'redis' => [
    'dsn' => 'redis://127.0.0.1:6379',
    'prefix' => 'ce:',
  ],

  'sqs' => [
    'region' => 'eu-central-1',
    'queue_url' => '',
    'access_key' => '',
    'secret_key' => '',
  ],

  'rabbitmq' => [
    'dsn' => 'amqp://guest:guest@127.0.0.1:5672',
    'queue' => 'cajeer-default',
  ],
];
