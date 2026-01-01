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
    'template' => [
        'debug' => true,
        'cache_dir' => ROOT_PATH . '/storage/compiled_tpl',
        'templates_dir' => ROOT_PATH . '/templates',
    ],
    'cache' => [
        'enabled' => true,
        'driver' => 'file',
        'path' => ROOT_PATH . '/storage/cache',
        'page_ttl' => 60,        // seconds
        'fragment_ttl' => 300,   // seconds
        'vary_by_query' => true,
    ],
    'seo' => [
        'site_name' => 'CajeerEngine',
        'default_title' => 'CajeerEngine',
        'default_description' => 'Open-source CMS (CajeerEngine) — third generation (2025).',
        'base_url' => 'http://localhost',
    ],
];
