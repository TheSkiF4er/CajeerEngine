<?php
namespace Modules\AI;

class Controller
{
    protected function client()
    {
        $kernel = $GLOBALS['CE_KERNEL'] ?? null;
        return ($kernel && method_exists($kernel, 'get')) ? $kernel->get('ai') : null;
    }

    public function suggestContent()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->suggestContent($_POST + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function suggestLayout()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->suggestLayout($_POST + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function adminCopilot()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->adminCopilot($_POST + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
