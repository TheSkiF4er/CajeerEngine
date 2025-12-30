<?php
namespace Modules\Compliance;

use Security\Compliance;

class Controller
{
    public function soc2()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;

        header('Content-Type: application/json');
        echo json_encode(Compliance::generateSOC2($tenantId, $from, $to), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
