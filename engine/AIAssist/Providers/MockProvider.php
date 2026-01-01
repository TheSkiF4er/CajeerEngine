<?php
namespace AIAssist\Providers;

use AIAssist\ProviderInterface;

class MockProvider implements ProviderInterface
{
    public function id(): string { return 'mock'; }

    public function chat(array $payload): array
    {
        $t0 = microtime(true);
        $messages = $payload['messages'] ?? [];
        $last = '';
        if (is_array($messages) && count($messages)) {
            $m = $messages[count($messages)-1];
            $last = is_array($m) ? (string)($m['content'] ?? '') : (string)$m;
        }

        $resp = ['role'=>'assistant','content'=>"Mock AI response (safe): ".mb_substr($last, 0, 200)];
        $ms = (int)round((microtime(true)-$t0)*1000);
        return ['ok'=>true,'response'=>['message'=>$resp],'latency_ms'=>$ms];
    }
}
