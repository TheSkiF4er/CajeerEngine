<?php
namespace AIAssist;

use AIAssist\Providers\MockProvider;
use AIAssist\Providers\HttpJsonProvider;
use Automation\PredictiveAlerts;

class AIClient
{
    protected array $cfg = [];
    protected Governance $gov;

    public function __construct(array $cfg = [])
    {
        $this->cfg = is_file(ROOT_PATH . '/system/ai.php') ? require ROOT_PATH . '/system/ai.php' : [];
        $this->gov = new Governance($this->cfg);
    }

    public function policy(int $tenantId): array { return $this->gov->policy($tenantId); }
    public function setOptIn(int $tenantId, bool $optIn): void { $this->gov->setOptIn($tenantId, $optIn); }

    protected function provider(string $id): ?ProviderInterface
    {
        $providers = $this->cfg['providers'] ?? [];
        if ($id === 'http') {
            $pcfg = $providers['http'] ?? [];
            if (!($pcfg['enabled'] ?? false)) return null;
            return new HttpJsonProvider($pcfg);
        }
        if ($id === 'mock') {
            $pcfg = $providers['mock'] ?? ['enabled'=>true];
            if (!($pcfg['enabled'] ?? true)) return null;
            return new MockProvider();
        }
        return null;
    }

    protected function redactor(): Redactor
    {
        $g = $this->cfg['governance'] ?? [];
        $rules = $g['redaction']['rules'] ?? [];
        return new Redactor(is_array($rules) ? $rules : []);
    }

    protected function guard(array $context, array $policy): array
    {
        if (!($policy['opt_in'] ?? false)) return ['ok'=>false,'status'=>'blocked','reason'=>'tenant_opt_out'];
        $allow = $policy['allow'] ?? [];
        $flags = $context['flags'] ?? [];
        foreach (['secrets','logs','pii'] as $f) {
            if (($flags[$f] ?? false) && !($allow[$f] ?? false)) {
                return ['ok'=>false,'status'=>'blocked','reason'=>'boundary_'.$f];
            }
        }
        return ['ok'=>true];
    }

    protected function normalizeMessages(array $context): array
    {
        $messages = $context['messages'] ?? [];
        if (!is_array($messages)) $messages = [];
        $red = $this->redactor();
        foreach ($messages as &$m) {
            if (is_array($m) && isset($m['content'])) $m['content'] = $red->apply((string)$m['content']);
        }
        return $messages;
    }

    protected function run(string $purpose, array $context): array
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $userId = isset($context['user_id']) ? (int)$context['user_id'] : null;

        $policy = $this->policy($tenantId);
        $guard = $this->guard($context, $policy);
        if (!$guard['ok']) {
            if ($policy['store_requests'] ?? true) {
                PromptStore::save([
                  'tenant_id'=>$tenantId,'user_id'=>$userId,
                  'provider'=>'none','model'=>null,'purpose'=>$purpose,
                  'prompt'=>$context,'response'=>null,'latency_ms'=>0,
                  'status'=>'blocked','reason'=>$guard['reason'],
                ]);
            }
            return ['ok'=>false,'error'=>'blocked','reason'=>$guard['reason']];
        }

        $providerId = (string)($context['provider'] ?? ($this->cfg['defaults']['provider'] ?? 'mock'));
        $provider = $this->provider($providerId);
        if (!$provider) return ['ok'=>false,'error'=>'provider_unavailable'];

        $payload = [
          'model' => (string)($context['model'] ?? ($this->cfg['defaults']['model'] ?? 'default')),
          'messages' => $this->normalizeMessages($context),
          'meta' => ['tenant_id'=>$tenantId,'purpose'=>$purpose],
        ];

        $res = $provider->chat($payload);
        $status = ($res['ok'] ?? false) ? 'ok' : 'failed';

        if ($policy['store_requests'] ?? true) {
            PromptStore::save([
              'tenant_id'=>$tenantId,'user_id'=>$userId,
              'provider'=>$providerId,'model'=>$payload['model'],'purpose'=>$purpose,
              'prompt'=>$payload,'response'=>$res,
              'tokens_in'=>$res['tokens_in'] ?? null,'tokens_out'=>$res['tokens_out'] ?? null,
              'latency_ms'=>$res['latency_ms'] ?? null,'status'=>$status,'reason'=>$res['error'] ?? null,
            ]);
        }

        if (!($res['ok'] ?? false)) return ['ok'=>false,'error'=>$res['error'] ?? 'provider_error'];
        return ['ok'=>true,'provider'=>$providerId,'model'=>$payload['model'],'result'=>$res['response'] ?? $res];
    }

    public function suggestContent(array $context): array { return $this->run('content', $context); }
    public function suggestLayout(array $context): array { return $this->run('layout', $context); }
    public function adminCopilot(array $context): array { return $this->run('admin', $context); }

    public function alerts(array $context): array
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        PredictiveAlerts::create('info', 'AI alert run executed', ['context'=>$context], $tenantId);
        return ['ok'=>true,'status'=>'created'];
    }
}
