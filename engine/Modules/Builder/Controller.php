<?php
namespace Modules\Builder;

use UIBuilder\Pro\Collab;

class Controller
{
    public function lock()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $userId = (int)($_SERVER['HTTP_X_CE_USER_ID'] ?? 0);
        if ($userId<=0) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); return; }

        $docType = (string)($_POST['doc_type'] ?? ($_GET['doc_type'] ?? 'page'));
        $docId = (string)($_POST['doc_id'] ?? ($_GET['doc_id'] ?? ''));
        if ($docId==='') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'doc_id_required']); return; }

        $ttl = (int)($_POST['ttl'] ?? ($_GET['ttl'] ?? 30));
        $res = Collab::acquire($docType, $docId, $userId, $ttl, $tenantId);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function patch()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $userId = (int)($_SERVER['HTTP_X_CE_USER_ID'] ?? 0);
        if ($userId<=0) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); return; }

        $docType = (string)($_POST['doc_type'] ?? 'page');
        $docId = (string)($_POST['doc_id'] ?? '');
        $patchJson = (string)($_POST['patch_json'] ?? '');

        if ($docId==='' || $patchJson==='') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'doc_id_and_patch_required']); return; }

        $patch = json_decode($patchJson, true);
        if (!is_array($patch)) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'bad_patch_json']); return; }

        $res = Collab::appendPatch($docType, $docId, $userId, $patch, $tenantId);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
