<?php
namespace Modules\AI;

use AIAssist\PromptStore;
use AIAssist\Recommendations;

class Controller
{
    protected function client()
    {
        $kernel = $GLOBALS['CE_KERNEL'] ?? null;
        return ($kernel && method_exists($kernel, 'get')) ? $kernel->get('ai') : null;
    }

    protected function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $j = json_decode($raw ?: '', true);
        return is_array($j) ? $j : [];
    }

    public function policy()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'policy'=>$ai->policy($tenantId)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function optIn()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $body = $this->jsonBody();
        $opt = (bool)($body['opt_in'] ?? ($_GET['opt_in'] ?? false));
        $ai->setOptIn($tenantId, $opt);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'opt_in'=>$opt], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function suggestContent()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->suggestContent($this->jsonBody() + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function suggestLayout()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->suggestLayout($this->jsonBody() + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function adminCopilot()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->adminCopilot($this->jsonBody() + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function requests()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $policy = $ai->policy($tenantId);
        if (!($policy['transparency'] ?? true)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'transparency_disabled']); return; }

        $purpose = (string)($_GET['purpose'] ?? '');
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>PromptStore::list($tenantId, $purpose, $limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function request()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $policy = $ai->policy($tenantId);
        if (!($policy['transparency'] ?? true)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'transparency_disabled']); return; }

        $id = (int)($_GET['id'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'row'=>PromptStore::get($tenantId, $id)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function recommendRun()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $kernel = $GLOBALS['CE_KERNEL'] ?? null;
        $pdo = $kernel && method_exists($kernel,'get') ? \Database\DB::pdo() : null;

        if ($pdo) {
            $sql1 = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
            $sql2 = ROOT_PATH . '/system/sql/ai_v3_7.sql';
            if (is_file($sql1)) $pdo->exec(file_get_contents($sql1));
            if (is_file($sql2)) $pdo->exec(file_get_contents($sql2));

            $st = $pdo->prepare("SELECT path, MAX(duration_ms) mx FROM ce_perf_requests WHERE tenant_id=:t AND ts >= DATE_SUB(NOW(), INTERVAL 24 HOUR) GROUP BY path ORDER BY mx DESC LIMIT 5");
            $st->execute([':t'=>$tenantId]);
            $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($rows as $r) {
                $path = (string)($r['path'] ?? '');
                $mx = (int)($r['mx'] ?? 0);
                if ($mx >= 1000) {
                    Recommendations::add($tenantId, 'perf', 'Обнаружены медленные запросы: '.$path, ['max_ms'=>$mx,'hint'=>'Рассмотрите cache и оптимизацию SQL'], 'heuristic');
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'status'=>'generated'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function recommendations()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $status = (string)($_GET['status'] ?? 'open');
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>Recommendations::list($tenantId, $status, $limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function alertsRun()
    {
        $ai = $this->client();
        if (!$ai) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'ai_not_available']); return; }
        header('Content-Type: application/json');
        echo json_encode($ai->alerts($this->jsonBody() + $_GET), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
