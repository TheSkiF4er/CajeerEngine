<?php
namespace Modules\ControlPlane;

use ControlPlane\Auth;
use ControlPlane\FleetManager;
use ControlPlane\PolicyManager;
use ControlPlane\Insights;
use ControlPlane\HealthScorer;
use ControlPlane\Forecasting;
use ControlPlane\RolloutManager;
use ControlPlane\SelfHealing;

class Controller
{
    protected function cfg(): array
    {
        return is_file(ROOT_PATH . '/system/control_plane.php') ? require ROOT_PATH . '/system/control_plane.php' : ['enabled'=>false];
    }

    protected function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $j = json_decode($raw ?: '', true);
        return is_array($j) ? $j : [];
    }

    protected function guard(): array
    {
        $cfg = $this->cfg();
        if (!($cfg['enabled'] ?? false)) { http_response_code(503); return ['ok'=>false,'error'=>'cp_disabled']; }
        Auth::requireToken($cfg);
        return $cfg;
    }

    public function status()
    {
        $cfg = $this->guard();
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'version'=>trim(@file_get_contents(ROOT_PATH.'/system/version.txt')),'control_plane'=>['enabled'=>true]], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function fleet()
    {
        $this->guard();
        $body = $this->jsonBody();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            FleetManager::register($body + $_GET);
        }
        $tenant = (int)($_GET['tenant_id'] ?? 0);
        $status = (string)($_GET['status'] ?? '');
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>FleetManager::list($tenant,$status)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function policyGet()
    {
        $this->guard();
        $scope = (string)($_GET['scope'] ?? 'global');
        $tenant = (int)($_GET['tenant_id'] ?? 0);
        $site = (int)($_GET['site_id'] ?? 0);
        $key = (string)($_GET['key'] ?? 'policies');
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'policy'=>PolicyManager::get($scope,$tenant,$site,$key)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function policySet()
    {
        $this->guard();
        $b = $this->jsonBody() + $_GET;
        $scope = (string)($b['scope'] ?? 'global');
        $tenant = (int)($b['tenant_id'] ?? 0);
        $site = (int)($b['site_id'] ?? 0);
        $key = (string)($b['key'] ?? 'policies');
        $val = $b['value'] ?? [];
        if (!is_array($val)) $val = ['value'=>$val];
        PolicyManager::set($scope,$tenant,$site,$key,$val);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'status'=>'saved'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function insightsTenants()
    {
        $cfg = $this->guard();
        $w = (int)($_GET['window_hours'] ?? ($cfg['insights']['window_hours'] ?? 24));
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'perf'=>Insights::crossTenant($w),'errors'=>Insights::errorsByTenant($w)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function healthCompute()
    {
        $cfg = $this->guard();
        $tenant = (int)($_GET['tenant_id'] ?? 0);
        $site = (int)($_GET['site_id'] ?? 0);
        $w = (int)($_GET['window_hours'] ?? ($cfg['insights']['window_hours'] ?? 24));
        $slow = (int)($_GET['slow_ms'] ?? ($cfg['insights']['slow_ms'] ?? 1000));
        $thr = (float)($_GET['error_rate_threshold'] ?? ($cfg['insights']['error_rate_threshold'] ?? 0.02));
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true] + HealthScorer::compute($tenant,$site,$w,$slow,$thr), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function capacityForecast()
    {
        $cfg = $this->guard();
        $tenant = (int)($_GET['tenant_id'] ?? 0);
        $site = (int)($_GET['site_id'] ?? 0);
        $w = (int)($_GET['window_hours'] ?? ($cfg['insights']['window_hours'] ?? 24));
        header('Content-Type: application/json');
        echo json_encode(Forecasting::estimate($tenant,$site,$w), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function rolloutPlan()
    {
        $cfg = $this->guard();
        $b = $this->jsonBody() + $_GET;
        $scope = (string)($b['scope'] ?? 'tenant');
        $tenant = (int)($b['tenant_id'] ?? 0);
        $site = (int)($b['site_id'] ?? 0);
        $target = (string)($b['target_version'] ?? trim(@file_get_contents(ROOT_PATH.'/system/version.txt')));
        $policy = PolicyManager::resolve($cfg['policies'] ?? [], $tenant, $site);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'plan'=>RolloutManager::plan($scope,$tenant,$site,$target,$policy)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function rolloutCreate()
    {
        $cfg = $this->guard();
        $b = $this->jsonBody() + $_GET;
        $scope = (string)($b['scope'] ?? 'tenant');
        $tenant = (int)($b['tenant_id'] ?? 0);
        $site = (int)($b['site_id'] ?? 0);
        $target = (string)($b['target_version'] ?? trim(@file_get_contents(ROOT_PATH.'/system/version.txt')));
        $policy = PolicyManager::resolve($cfg['policies'] ?? [], $tenant, $site);
        $plan = RolloutManager::plan($scope,$tenant,$site,$target,$policy);
        header('Content-Type: application/json');
        echo json_encode(RolloutManager::create($plan), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function rolloutList()
    {
        $this->guard();
        $status = (string)($_GET['status'] ?? '');
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>RolloutManager::list($status)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function rolloutStep()
    {
        $this->guard();
        $id = (int)($_GET['id'] ?? 0);
        $tenant = (int)($_GET['tenant_id'] ?? 0);
        $health = HealthScorer::compute($tenant,0,24,1000,0.02)['score'] ?? 100;
        header('Content-Type: application/json');
        echo json_encode(RolloutManager::step($id, (int)$health), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function healEnqueue()
    {
        $this->guard();
        $b = $this->jsonBody() + $_GET;
        $tenant = (int)($b['tenant_id'] ?? 0);
        $site = (int)($b['site_id'] ?? 0);
        $kind = (string)($b['kind'] ?? 'flush_cache');
        $input = $b['input'] ?? [];
        if (!is_array($input)) $input = ['value'=>$input];
        header('Content-Type: application/json');
        echo json_encode(SelfHealing::enqueue($tenant,$site,$kind,$input), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function healRun()
    {
        $this->guard();
        header('Content-Type: application/json');
        echo json_encode(SelfHealing::runOne(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
