<?php
return [
  'enabled' => true,

  'auth' => [
    'token' => getenv('CE_CP_TOKEN') ?: '',
    'header' => 'X-Control-Plane-Token',
  ],

  'policies' => [
    'rollouts' => [
      'default_strategy' => 'canary', // canary|staged|all_at_once
      'max_percent_per_step' => 20,
      'step_delay_sec' => 300,
      'health_gate' => true,
    ],
    'self_healing' => [
      'enabled' => true,
      'auto_restart_workers' => true,
      'cache_flush_on_errors' => true,
    ],
  ],

  'insights' => [
    'window_hours' => 24,
    'slow_ms' => 1000,
    'error_rate_threshold' => 0.02,
  ],
];
