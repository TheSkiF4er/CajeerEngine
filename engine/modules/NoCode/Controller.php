<?php
namespace Modules\NoCode;

use NoCode\Workflows;
use NoCode\Forms;

class Controller
{
    public function runWorkflow()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $slug = (string)($_POST['slug'] ?? ($_GET['slug'] ?? ''));
        if ($slug==='') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'slug_required']); return; }

        $payload = [];
        if (!empty($_POST['payload_json'])) {
            $payload = json_decode((string)$_POST['payload_json'], true) ?: [];
        }

        $res = Workflows::run($slug, $payload, $tenantId);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function submitForm()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $slug = (string)($_POST['slug'] ?? ($_GET['slug'] ?? ''));
        if ($slug==='') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'slug_required']); return; }

        $payload = [];
        if (!empty($_POST['payload_json'])) {
            $payload = json_decode((string)$_POST['payload_json'], true) ?: [];
        } else {
            // accept raw POST key-values as payload
            $payload = $_POST;
            unset($payload['slug'], $payload['payload_json']);
        }

        $res = Forms::submit($slug, $payload, $tenantId);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
