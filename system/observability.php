<?php
return [
  'logger' => [
    'enabled' => true,
    'channel' => 'app',
    'path' => ROOT_PATH . '/storage/logs/app.jsonl',
    'level' => 'info',
  ],
  'tracing' => [
    'enabled' => true,
    'header_request_id' => 'X-Request-Id',
    'propagate_to_response' => true,
  ],
  'db' => [
    'trace_queries' => true,
    'slow_ms' => 250,
  ],
  'metrics' => [
    'enabled' => true,
    'path' => '/metrics',
  ],
  'health' => [
    'enabled' => true,
  ],
];
