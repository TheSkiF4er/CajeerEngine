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
        require_once $path;
    }
});

defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__));
defined('PUBLIC_PATH') || define('PUBLIC_PATH', ROOT_PATH . '/public');

if (!defined('CAJEER_BOOT_KERNEL') || CAJEER_BOOT_KERNEL !== false) {
    require_once __DIR__ . '/init.php';
}
