<?php
return [
  'analytics' => [
    'enabled' => true,
    'sample_rate' => 1.0,
    'retention_days' => 30,
  ],

  'performance' => [
    'enabled' => true,
    'slow_query_ms' => 200,
    'slow_request_ms' => 500,
  ],

  'cost' => [
    'enabled' => true,
    // cost units are abstract in baseline. Real cloud cost adapters in 3.5.x
    'unit' => 'credits',
  ],

  'automation' => [
    'enabled' => true,
    'policies' => [
      // Example:
      // ['id'=>'scale_workers', 'type'=>'autoscale', 'metric'=>'queue_depth', 'gt'=>1000, 'action'=>['workers'=>'+2']],
    ],
  ],

  'ai_assist' => [
    'enabled' => false,
    'provider' => 'none', // none|openai|custom
    'endpoint' => '',
    'token' => '',
  ],
];
