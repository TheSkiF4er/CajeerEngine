<?php
/**
 * CajeerEngine Admin entrypoint.
 */
declare(strict_types=1);

if (!ob_get_level()) {
    ob_start();
}

require_once __DIR__ . '/../engine/bootstrap.php';

if (ob_get_level()) {
    @ob_end_flush();
}
