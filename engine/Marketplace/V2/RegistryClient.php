<?php
namespace Marketplace\V2;

use Observability\Logger;

class RegistryClient
{
    public function __construct(protected array $registry) {}

    protected function base(): string { return rtrim((string)$this->registry['base_url'], '/'); }

    protected function headers(): array
    {
        $h = ['Accept: application/json'];
        $token = (string)($this->registry['token'] ?? '');
        if ($token !== '') $h[] = 'Authorization: Bearer ' . $token;
        return $h;
    }

    public function search(string $q, string $type = ''): array
    {
        $url = $this->base() . '/v2/search?q=' . rawurlencode($q);
        if ($type !== '') $url .= '&type=' . rawurlencode($type);

        Logger::info('marketplace.v2.search', ['q'=>$q,'type'=>$type,'registry'=>$this->registry['name'] ?? '']);
        $r = Http::request('GET', $url, $this->headers());
        if (!$r['ok']) return ['ok'=>false,'error'=>$r['error'] ?: ('http_' . $r['status'])];

        $data = json_decode($r['body'], true);
        if (!is_array($data)) return ['ok'=>false,'error'=>'bad_json'];
        return ['ok'=>true,'items'=>$data['items'] ?? []];
    }

    public function fetchManifest(string $packageId): array
    {
        $url = $this->base() . '/v2/package/' . rawurlencode($packageId) . '/manifest';
        Logger::info('marketplace.v2.manifest', ['package'=>$packageId]);
        $r = Http::request('GET', $url, $this->headers());
        if (!$r['ok']) return ['ok'=>false,'error'=>$r['error'] ?: ('http_' . $r['status'])];

        $data = json_decode($r['body'], true);
        if (!is_array($data)) return ['ok'=>false,'error'=>'bad_json'];
        return ['ok'=>true,'manifest'=>$data];
    }

    public function download(string $packageId): array
    {
        $url = $this->base() . '/v2/package/' . rawurlencode($packageId) . '/download';
        Logger::info('marketplace.v2.download', ['package'=>$packageId]);
        $r = Http::request('GET', $url, array_merge($this->headers(), ['Accept: application/octet-stream']));
        if (!$r['ok']) return ['ok'=>false,'error'=>$r['error'] ?: ('http_' . $r['status'])];
        return ['ok'=>true,'bytes'=>$r['body']];
    }
}
