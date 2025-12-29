<?php
namespace Observability;

use Database\DB;

class Metrics
{
    public static function renderPrometheus(): string
    {
        $tenant = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $dur = (float)($_SERVER['CE_REQ_MS'] ?? 0);
        $out = [];
        $out[] = "# HELP cajeer_requests_total Total requests observed";
        $out[] = "# TYPE cajeer_requests_total counter";
        $out[] = "cajeer_requests_total{tenant_id=\"$tenant\"} " . (int)($_SERVER['CE_REQ_COUNT'] ?? 1);
        $out[] = "# HELP cajeer_request_duration_ms Request duration in ms";
        $out[] = "# TYPE cajeer_request_duration_ms gauge";
        $out[] = "cajeer_request_duration_ms " . $dur;
        $out[] = "# HELP cajeer_db_connected Database connection status";
        $out[] = "# TYPE cajeer_db_connected gauge";
        $out[] = "cajeer_db_connected " . (DB::pdo() ? 1 : 0);
        return implode("\n", $out) . "\n";
    }
}
