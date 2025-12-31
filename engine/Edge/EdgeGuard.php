<?php
namespace Edge;

class EdgeGuard
{
    public static function enforce(array $cfg): void
    {
        $role = (string)($cfg['mode']['role'] ?? 'origin');
        $readonly = (bool)($cfg['mode']['readonly_edge'] ?? false);

        if ($role !== 'edge_readonly' || !$readonly) return;

        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $write = in_array($method, ['POST','PUT','PATCH','DELETE'], true);

        if ($write) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['ok'=>false,'error'=>'edge_readonly','message'=>'Write operations are disabled on edge nodes'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}
