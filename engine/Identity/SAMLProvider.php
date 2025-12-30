<?php
namespace Identity;

use Observability\Logger;

/**
 * SAML provider (production wiring).
 * XML signature validation is a foundation stub (3.3.x to harden).
 */
class SAMLProvider implements IdentityProviderContract
{
    public function __construct(protected string $name, protected array $cfg) {}

    public function type(): string { return 'saml'; }
    public function name(): string { return $this->name; }

    public function startAuth(array $params = []): array
    {
        // Foundation: build AuthnRequest and redirect to IdP SSO endpoint from metadata
        $url = (string)($this->cfg['idp_sso_url'] ?? '');
        if ($url === '') return ['ok'=>false,'error'=>'idp_sso_url_missing'];
        $relay = bin2hex(random_bytes(16));
        $_SESSION['saml_relay'] = $relay;

        Logger::info('saml.start', ['provider'=>$this->name,'note'=>'authn_request_stub']);
        return ['ok'=>true,'redirect'=>$url,'relay_state'=>$relay];
    }

    public function handleCallback(array $params = []): array
    {
        $relay = (string)($params['RelayState'] ?? '');
        if ($relay === '' || $relay !== ($_SESSION['saml_relay'] ?? '')) {
            return ['ok'=>false,'error'=>'bad_relay'];
        }
        // Foundation: parse SAMLResponse, validate, extract NameID/attributes
        $sub = (string)($params['NameID'] ?? 'saml_subject');
        $claims = (array)($params['claims'] ?? []);
        Logger::info('saml.callback', ['provider'=>$this->name,'subject'=>$sub,'note'=>'xml_validation_stub']);
        return ['ok'=>true,'subject'=>$sub,'claims'=>$claims,'scopes'=>$claims['scope'] ?? []];
    }
}
