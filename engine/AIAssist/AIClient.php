<?php
namespace AIAssist;

class AIClient
{
    public function __construct(protected array $cfg = []) {}

    public function enabled(): bool { return (bool)($this->cfg['ai_assist']['enabled'] ?? false); }

    public function suggestContent(array $context): array
    {
        if (!$this->enabled()) return ['ok'=>false,'error'=>'disabled'];
        // Foundation: external provider call not implemented in baseline
        return ['ok'=>false,'error'=>'provider_not_configured'];
    }

    public function suggestLayout(array $context): array
    {
        if (!$this->enabled()) return ['ok'=>false,'error'=>'disabled'];
        return ['ok'=>false,'error'=>'provider_not_configured'];
    }

    public function adminCopilot(array $context): array
    {
        if (!$this->enabled()) return ['ok'=>false,'error'=>'disabled'];
        return ['ok'=>false,'error'=>'provider_not_configured'];
    }
}
