<?php
namespace Modules\Edge;

use Edge\RegionRouter;

class Controller
{
    protected function cfg(): array
    {
        return is_file(ROOT_PATH . '/system/edge.php') ? require ROOT_PATH . '/system/edge.php' : [];
    }

    public function config()
    {
        $cfg = $this->cfg();
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'edge'=>$cfg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function canary()
    {
        $cfg = $this->cfg();
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'canary'=>RegionRouter::shouldCanary($cfg)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function route()
    {
        $cfg = $this->cfg();
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $region = RegionRouter::currentRegion($cfg);
        $role = RegionRouter::role($cfg);

        $path = (string)($_GET['path'] ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));
        $decision = ($role === 'edge_readonly') ? 'edge' : 'origin';

        RegionRouter::logDecision($tenantId, $region, $decision, $path, 200, null);

        header('Content-Type: application/json');
        echo json_encode([
          'ok'=>true,
          'region'=>$region,
          'role'=>$role,
          'decision'=>$decision,
          'path'=>$path,
          'canary'=>RegionRouter::shouldCanary($cfg),
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
