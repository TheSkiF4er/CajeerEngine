<?php
return [
  'app' => [
    'debug' => true,
    'base_url' => 'http://localhost',
    'timezone' => 'UTC',
    'default_language' => 'ru',
  ],
  'rarog' => [
    'enabled' => true,
    'assets_path' => '/assets/rarog',
  ],
,
    'template' => [
        'debug' => true,
        'cache_dir' => ROOT_PATH . '/storage/compiled_tpl',
        'templates_dir' => ROOT_PATH . '/templates',
    ],
];
