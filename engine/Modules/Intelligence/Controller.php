<?php
namespace Modules\Intelligence;

use Database\DB;

class Controller
{
    public function usage()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $from = $_GET['from'] ?? date('Y-m-d 00:00:00');
        $to = $_GET['to'] ?? date('Y-m-d 23:59:59');

        $pdo = DB::pdo(); if(!$pdo) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'db_required']); return; }
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));

        $st = $pdo->prepare("SELECT event_type, COUNT(*) c, SUM(value) v FROM ce_usage_events WHERE tenant_id=:t AND ts BETWEEN :f AND :to GROUP BY event_type ORDER BY c DESC");
        $st->execute([':t'=>$tenantId,':f'=>$from,':to'=>$to]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function perf()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $from = $_GET['from'] ?? date('Y-m-d 00:00:00');
        $to = $_GET['to'] ?? date('Y-m-d 23:59:59');

        $pdo = DB::pdo(); if(!$pdo) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'db_required']); return; }
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));

        $st = $pdo->prepare("SELECT path, COUNT(*) c, AVG(duration_ms) avg_ms, MAX(duration_ms) max_ms FROM ce_perf_requests WHERE tenant_id=:t AND ts BETWEEN :f AND :to GROUP BY path ORDER BY max_ms DESC LIMIT 50");
        $st->execute([':t'=>$tenantId,':f'=>$from,':to'=>$to]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function cost()
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $from = $_GET['from'] ?? date('Y-m-d 00:00:00');
        $to = $_GET['to'] ?? date('Y-m-d 23:59:59');

        $pdo = DB::pdo(); if(!$pdo) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'db_required']); return; }
        $sql = ROOT_PATH . '/system/sql/intelligence_v3_5.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));

        $st = $pdo->prepare("SELECT category, SUM(amount) total, unit FROM ce_cost_ledger WHERE tenant_id=:t AND ts BETWEEN :f AND :to GROUP BY category, unit ORDER BY total DESC");
        $st->execute([':t'=>$tenantId,':f'=>$from,':to'=>$to]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
