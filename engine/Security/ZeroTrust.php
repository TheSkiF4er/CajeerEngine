<?php
namespace Security;

use Observability\Logger;

class ZeroTrust
{
    public function __construct(
        protected PolicyEngine $policy,
        protected array $cfg = []
    ) {}

    public function buildContext(): AuthContext
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        // Foundation: user/scopes come from headers or session.
        $userId = null;
        $scopes = [];

        if (!empty($_SERVER['HTTP_X_CE_USER_ID'])) $userId = (int)$_SERVER['HTTP_X_CE_USER_ID'];
        if (!empty($_SERVER['HTTP_X_CE_SCOPES'])) {
            $scopes = array_values(array_filter(array_map('trim', explode(',', (string)$_SERVER['HTTP_X_CE_SCOPES']))));
        }

        $deviceId = $_SERVER['HTTP_X_CE_DEVICE_ID'] ?? null;

        $trust = 0;
        // Device posture foundation: trust score can be passed or later resolved from DB
        if (!empty($_SERVER['HTTP_X_CE_DEVICE_TRUST'])) $trust = (int)$_SERVER['HTTP_X_CE_DEVICE_TRUST'];

        return new AuthContext($tenantId, $userId, $scopes, $deviceId, $trust, []);
    }

    public function authorize(AuthContext $ctx, array $req): array
    {
        $enabled = (bool)($this->cfg['enabled'] ?? true);
        if (!$enabled) return ['allow'=>true,'reason'=>'disabled'];

        // Device posture (foundation)
        $dp = (array)($this->cfg['device_posture'] ?? []);
        if (($dp['enabled'] ?? false) && $ctx->deviceTrust < (int)($dp['min_trust'] ?? 50)) {
            return ['allow'=>false,'reason'=>'device_posture_low'];
        }

        return $this->policy->decide($ctx, $req);
    }

    public function logDecision(AuthContext $ctx, array $req, int $status, bool $allow, string $reason): void
    {
        $al = (array)($this->cfg['access_logs'] ?? []);
        if (!($al['enabled'] ?? true)) return;

        $row = [
            'tenant_id' => $ctx->tenantId,
            'user_id' => $ctx->userId,
            'method' => (string)($req['method'] ?? ''),
            'path' => (string)($req['path'] ?? ''),
            'status' => $status,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'scopes_json' => json_encode($ctx->scopes, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'decision' => $allow ? 'allow' : 'deny',
            'reason' => $reason,
            'ts' => date('Y-m-d H:i:s'),
        ];
        AccessLog::append($row);
        Logger::info('zero_trust.decision', ['allow'=>$allow,'reason'=>$reason,'path'=>$row['path']]);
    }
}
