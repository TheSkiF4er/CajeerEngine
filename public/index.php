<?php
/**
 * CajeerEngine Frontend entrypoint.
 *
 * IMPORTANT:
 * Do not send headers after the kernel started rendering output.
 * All response codes/headers should be handled inside the kernel/router layer.
 */
declare(strict_types=1);

// Start buffering early to prevent accidental output (warnings/notices) breaking headers.
if (!ob_get_level()) {
    ob_start();
}

require_once __DIR__ . '/../engine/bootstrap.php';

// If bootstrap/kernel already produced output, just flush safely.
if (ob_get_level()) {
    @ob_end_flush();
}
