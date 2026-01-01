<?php
namespace Modules\PlatformV4;

use ControlPlane\Auth;
use PlatformSDK\Sdk;
use IaC\Generator;
use Intent\IntentStore;
use Intent\Reconciler;
use EventMesh\Mesh;
use MarketplaceV4\Registry;

class Controller
{
    protected function cpCfg(): array
    {
        return is_file(ROOT_PATH . '/system/control_plane.php') ? require ROOT_PATH . '/system/control_plane.php' : [];
    }

    protected function guard(): void
    {
        Auth::requireToken($this->cpCfg());
    }

    protected function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $j = json_decode($raw ?: '', true);
        return is_array($j) ? $j : [];
    }

    public function platformConfig()
    {
        $this->guard();
        header('Content-Type: text/yaml; charset=utf-8');
        echo Sdk::readPlatformYaml();
    }

    public function intents()
    {
        $this->guard();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $b = $this->jsonBody();
            $tenant = (int)($b['tenant_id'] ?? 0);
            $name = (string)($b['name'] ?? 'intent');
            $kind = (string)($b['kind'] ?? 'PolicyIntent');
            $desired = $b['desired'] ?? [];
            if (!is_array($desired)) $desired = ['value'=>$desired];
            $id = IntentStore::create($tenant, $name, $kind, $desired);
            header('Content-Type: application/json');
            echo json_encode(['ok'=>true,'id'=>$id], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            return;
        }

        $tenant = (int)($_GET['tenant_id'] ?? 0);
        $status = (string)($_GET['status'] ?? 'pending');
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>IntentStore::list($tenant,$status,$limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function reconcile()
    {
        $this->guard();
        $limit = (int)($_GET['limit'] ?? 25);
        header('Content-Type: application/json');
        echo json_encode(Reconciler::run('manual', $limit), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function iacDockerCompose()
    {
        $this->guard();
        header('Content-Type: text/yaml; charset=utf-8');
        echo Generator::dockerCompose();
    }

    public function iacKubernetes()
    {
        $this->guard();
        header('Content-Type: text/yaml; charset=utf-8');
        echo Generator::kubernetesDeployment();
    }

    public function eventMeshRecent()
    {
        $this->guard();
        $topic = (string)($_GET['topic'] ?? '');
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>Mesh::recent($topic,$limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function marketAI()
    {
        $this->guard();
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>Registry::listAI($limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function marketAutomation()
    {
        $this->guard();
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>Registry::listAutomation($limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function blueprints()
    {
        $this->guard();
        $limit = (int)($_GET['limit'] ?? 50);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'rows'=>Registry::listBlueprints($limit)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
