<?php
namespace Modules\Frontend;

use Frontend\ISRCache;

class Controller
{
    public function purgeIsr()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $key = (string)($_POST['surrogate'] ?? ($_GET['surrogate'] ?? ''));
        if ($key === '') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'surrogate_required']); return; }
        $n = ISRCache::purgeBySurrogate($key, $tenantId);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'purged'=>$n], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
