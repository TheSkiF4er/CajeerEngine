<?php
/**
 * DLE source connection settings for migration toolkit.
 * You can also override via CLI arguments (future).
 */
return [
  'db' => [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'dle',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
  ],
  // DLE table prefix if custom (default: dle_)
  'prefix' => 'dle_',
  // Import mode
  'import' => [
    'users' => true,
    'categories' => true,
    'posts' => true,
    'static_pages' => true
  ],
];
