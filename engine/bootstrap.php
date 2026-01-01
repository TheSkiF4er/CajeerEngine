<?php
/**
 * CajeerEngine
 * Author: TheSkiF4er
 * License: Apache-2.0
 */

// Simple native autoloader (no external deps)
spl_autoload_register(function(string $class): void {
    $base = __DIR__ . '/';
    $path = $base . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require $path;
    }
});

defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__));
defined('PUBLIC_PATH') || define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once __DIR__ . '/init.php';
