<?php
return [
  'default' => 'main',
  'hosts' => [
    'localhost' => 'main',
  ],
  'sites' => [
    'main' => [
      'title' => 'Main site',
      'base_url' => 'http://localhost',
      'theme' => 'default',
      'storage_prefix' => 'main',
      'db' => null,
    ],
  ],
];
