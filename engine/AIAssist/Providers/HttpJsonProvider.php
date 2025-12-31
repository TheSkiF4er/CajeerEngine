<?php
namespace AIAssist\Providers;

use AIAssist\ProviderInterface;

class HttpJsonProvider implements ProviderInterface
{
    public function __construct(protected array $cfg = []) {}
    public function id(): string { return 'http'; }

    public function chat(array $payload): array
    {
        $endpoint = (string)($this->cfg['endpoint'] ?? '');
        if ($endpoint === '') return ['ok'=>false,'error'=>'endpoint_missing'];

        $token = (string)($this->cfg['token'] ?? '');
        $timeoutMs = (int)($this->cfg['timeout_ms'] ?? 8000);

        $t0 = microtime(true);

        $headers = ['Content-Type: application/json'];
        if ($token !== '') $headers[] = 'Authorization: Bearer '.$token;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeoutMs);

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $ms = (int)round((microtime(true)-$t0)*1000);

        if ($raw === false) return ['ok'=>false,'error'=>'curl:'.$err,'latency_ms'=>$ms];
        if ($code < 200 || $code >= 300) return ['ok'=>false,'error'=>'http_'.$code,'latency_ms'=>$ms];

        $json = json_decode($raw, true);
        if (!is_array($json)) return ['ok'=>false,'error'=>'invalid_json','latency_ms'=>$ms];

        return ['ok'=>true,'response'=>$json,'latency_ms'=>$ms];
    }
}
