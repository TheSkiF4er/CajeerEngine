<?php
namespace Modules\Auth;

use Identity\OIDCProvider;
use Identity\SAMLProvider;
use Security\AccessLog;
use Observability\Logger;

class Controller
{
    protected function identityCfg(): array
    {
        return is_file(ROOT_PATH . '/system/identity.php') ? require ROOT_PATH . '/system/identity.php' : [];
    }

    public function oidcStart()
    {
        session_start();
        $cfg = $this->identityCfg();
        $name = $_GET['provider'] ?? 'corp';
        $p = ($cfg['oidc'][$name] ?? null);
        if (!$p) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'provider_not_found']); return; }

        $prov = new OIDCProvider($name, $p);
        $res = $prov->startAuth();
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function oidcCallback()
    {
        session_start();
        $cfg = $this->identityCfg();
        $name = $_GET['provider'] ?? 'corp';
        $p = ($cfg['oidc'][$name] ?? null);
        if (!$p) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'provider_not_found']); return; }

        $prov = new OIDCProvider($name, $p);
        $res = $prov->handleCallback($_GET);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function samlStart()
    {
        session_start();
        $cfg = $this->identityCfg();
        $name = $_GET['provider'] ?? 'corp';
        $p = ($cfg['saml'][$name] ?? null);
        if (!$p) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'provider_not_found']); return; }

        $prov = new SAMLProvider($name, $p);
        $res = $prov->startAuth();
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function samlAcs()
    {
        session_start();
        $cfg = $this->identityCfg();
        $name = $_GET['provider'] ?? 'corp';
        $p = ($cfg['saml'][$name] ?? null);
        if (!$p) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'provider_not_found']); return; }

        $prov = new SAMLProvider($name, $p);
        $res = $prov->handleCallback($_POST + $_GET);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
