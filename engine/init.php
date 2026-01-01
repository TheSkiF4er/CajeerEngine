<?php
/**
 * CajeerEngine
 * Author: TheSkiF4er
 * License: Apache-2.0
 */

use Core\Kernel;

register_shutdown_function(function() {
    try {
        $data = \Dev\Collector::finish();
        // Logs
        $cfg = \Dev\Logger::cfg();
        if (($cfg['enabled'] ?? false) && ($cfg['log_requests'] ?? false)) {
            \Dev\Logger::write('requests.log', ($data['request']['method'] ?? '') . ' ' . ($data['request']['uri'] ?? ''));
        }
        // Debug panel
        if (\Dev\DebugPanel::shouldShow()) {
            \Dev\DebugPanel::render($data);
        }
    } catch (\Throwable $e) {}
});

$kernel = new Kernel();
$kernel->boot();
