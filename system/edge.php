<?php
return [
  'mode' => [
    // core modes: origin|edge_readonly
    'role' => 'origin',
    'readonly_edge' => false,
  ],

  'regions' => [
    // region discovery priority: env -> config
    'current' => getenv('CE_REGION') ?: 'local',
    'available' => ['local'],
    // optional mapping for region-aware routing
    'routing' => [
      // 'eu' => ['origin_base' => 'https://origin-eu.example.com', 'edge_base' => 'https://edge-eu.example.com'],
    ],
  ],

  'data_locality' => [
    // enforce storage locality rules per tenant (foundation)
    'enforced' => false,
    // example: tenant_id => region
    'tenant_region_map' => [],
  ],

  'replication' => [
    'strategy' => 'none', // none|async|dualwrite (foundation)
    'conflict_resolution' => 'last_write_wins', // foundation
  ],

  'distributed_cache' => [
    'enabled' => true,
    'backend' => 'redis', // redis|none
    'prefix' => 'ce:',
  ],

  'event_bus' => [
    'enabled' => true,
    'backend' => 'redis', // redis|db (foundation)
    'topic_prefix' => 'ce:bus:',
  ],

  'edge_rendering' => [
    // Edge Rendering: production baseline (cacheable HTML + ESI-like includes foundation)
    'enabled' => true,
    'cache_ttl_sec' => 60,
  ],

  'ops' => [
    'canary' => [
      'enabled' => true,
      'header' => 'X-Canary',
      'percent' => 0, // 0..100
    ],
    'traffic_shaping' => [
      'enabled' => false,
      'max_rps' => 0,
    ],
  ],
];
