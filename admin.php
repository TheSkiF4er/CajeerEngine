<?php
/**
 * CajeerEngine Admin entrypoint.
 */
declare(strict_types=1);

if (!ob_get_level()) {
    ob_start();
}

define('CAJEER_BOOT_KERNEL', false);
require_once __DIR__ . '/../engine/bootstrap.php';

$kernel = new \Admin\AdminKernel();
$kernel->boot();

if (ob_get_level()) {
    @ob_end_flush();
}
