<?php
namespace Frontend;

use Template\Template;
use Observability\Logger;

class FrontendRuntime
{
    public function __construct(protected array $cfg = []) {}

    public function handle(string $uri, callable $originRender): array
    {
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $isr = (array)($this->cfg['runtime']['isr'] ?? []);
        $enabled = (bool)($isr['enabled'] ?? true);
        $ttl = (int)($isr['default_ttl_sec'] ?? 60);

        $cacheKey = 'uri:' . $uri;

        if ($enabled) {
            $hit = ISRCache::get($cacheKey, $tenantId);
            if ($hit) {
                return ['cached'=>true,'status'=>(int)$hit['status'],'content_type'=>(string)$hit['content_type'],'body'=>$hit['body'],'surrogate_keys'=>$hit['surrogate_keys'] ?? null];
            }
        }

        // Edge mode (foundation)
        $mode = (string)($this->cfg['runtime']['mode'] ?? 'origin');
        if ($mode === 'edge') {
            $edge = new EdgeRenderer();
            $res = $edge->render('auto', []);
            if ($res['ok'] ?? false) return $res;
        }

        // origin render
        $out = $originRender();

        if ($enabled) {
            ISRCache::put($cacheKey, (string)$out['body'], (string)($out['content_type'] ?? 'text/html'), (int)($out['status'] ?? 200), $ttl, $out['surrogate_keys'] ?? null, $tenantId);
        }

        return $out;
    }

    public function cdnHeaders(?string $surrogateKeys): array
    {
        $cdn = (array)($this->cfg['runtime']['cdn_native'] ?? []);
        if (!($cdn['enabled'] ?? false)) return [];
        $h = [];
        if (($cdn['surrogate_keys'] ?? true) && $surrogateKeys) {
            $h['Surrogate-Key'] = $surrogateKeys;
        }
        $h['Cache-Control'] = 'public, max-age=0, s-maxage=60';
        return $h;
    }
}
