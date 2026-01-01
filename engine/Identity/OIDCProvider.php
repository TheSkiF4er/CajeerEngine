<?php
namespace Identity;

use Observability\Logger;

/**
 * OIDC provider (production wiring).
 * Token verification is a foundation stub unless JWKS/issuer validation is configured.
 */
class OIDCProvider implements IdentityProviderContract
{
    public function __construct(protected string $name, protected array $cfg) {}

    public function type(): string { return 'oidc'; }
    public function name(): string { return $this->name; }

    public function startAuth(array $params = []): array
    {
        $issuer = rtrim((string)($this->cfg['issuer'] ?? ''), '/');
        $auth = $issuer . '/authorize';
        $clientId = (string)($this->cfg['client_id'] ?? '');
        $redirect = (string)($this->cfg['redirect_uri'] ?? '');
        $scopes = $this->cfg['scopes'] ?? ['openid','profile','email'];

        $state = bin2hex(random_bytes(16));
        $_SESSION['oidc_state'] = $state;

        $url = $auth . '?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirect,
            'scope' => implode(' ', $scopes),
            'state' => $state,
        ]);

        Logger::info('oidc.start', ['provider'=>$this->name]);
        return ['ok'=>true,'redirect'=>$url,'state'=>$state];
    }

    public function handleCallback(array $params = []): array
    {
        $state = (string)($params['state'] ?? '');
        if ($state === '' || $state !== ($_SESSION['oidc_state'] ?? '')) {
            return ['ok'=>false,'error'=>'bad_state'];
        }

        // Foundation: exchange code and verify ID token not implemented (requires JWKS validation)
        // Return placeholder subject.
        $sub = (string)($params['sub'] ?? 'oidc_subject');
        $claims = (array)($params['claims'] ?? []);
        Logger::info('oidc.callback', ['provider'=>$this->name,'subject'=>$sub,'note'=>'token_validation_stub']);
        return ['ok'=>true,'subject'=>$sub,'claims'=>$claims,'scopes'=>$claims['scope'] ?? []];
    }
}
