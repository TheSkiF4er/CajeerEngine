<?php
namespace Marketplace\V2;
use Observability\Logger;

class RegistryClient
{
    public function __construct(protected array $registries) {}
    public function search(string $q, string $type = ''): array
    {
        Logger::info('marketplace.v2.search', ['q'=>$q,'type'=>$type,'registries'=>count($this->registries)]);
        return ['ok'=>true,'items'=>[],'note'=>'Foundation stub. Implement HTTP client in 3.x minors.'];
    }
    public function fetchManifest(string $packageId): array
    {
        Logger::info('marketplace.v2.fetch_manifest', ['package'=>$packageId]);
        return ['ok'=>false,'error'=>'not_implemented'];
    }
}
