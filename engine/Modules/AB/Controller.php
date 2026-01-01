<?php
namespace Modules\AB;

use AB\ABTesting;

class Controller
{
    public function assign()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $exp = (string)($_GET['experiment'] ?? '');
        $userHash = (string)($_GET['user'] ?? '');
        if ($exp==='' || $userHash==='') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'experiment_and_user_required']); return; }

        $res = ABTesting::assign($exp, $userHash, $tenantId);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
