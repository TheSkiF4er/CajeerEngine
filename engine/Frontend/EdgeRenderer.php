<?php
namespace Frontend;

use Observability\Logger;

/**
 * Edge rendering foundation:
 * - In real deployments, this would run in edge runtime (Workers/Lambda@Edge).
 * - Here it's a stub adapter that can be swapped via config.
 */
class EdgeRenderer
{
    public function render(string $template, array $vars = []): array
    {
        Logger::info('frontend.edge.render', ['template'=>$template,'note'=>'foundation_stub']);
        // fallback: return empty so origin renderer is used
        return ['ok'=>false,'error'=>'not_implemented'];
    }
}
