<?php
namespace Modules\Automation;

class Controller
{
    public function runOnce()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $kernel = $GLOBALS['CE_KERNEL'] ?? null;
        $auto = $kernel && method_exists($kernel, 'get') ? $kernel->get('automation') : null;
        if (!$auto) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'automation_not_available']); return; }

        header('Content-Type: application/json');
        echo json_encode($auto->runOnce($tenantId), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
