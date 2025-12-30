<?php
return [
  'runtime' => [
    'mode' => 'origin', // origin|edge (edge foundation)
    'isr' => [
      'enabled' => true,
      'default_ttl_sec' => 60,
    ],
    'cdn_native' => [
      'enabled' => false,
      'surrogate_keys' => true,
    ],
  ],

  'ab_testing' => [
    'enabled' => false,
    'salt' => 'change-me',
  ],
];
