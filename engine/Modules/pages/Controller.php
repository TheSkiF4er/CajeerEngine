<?php
namespace Modules\pages;

use Content\Repository\ContentRepository;
use Template\Template;
use Core\KernelSingleton;

class Controller
{
    public function view(): void
    {
        $slug = (string)($_GET['slug'] ?? '');
        $repo = new ContentRepository();
        $it = $repo->findBySlug('page', $slug);
        if (!$it) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        try {
            $seo = KernelSingleton::container()->get('seo');
            if ($seo instanceof \Seo\MetaManager) {
                $seo->setTitle($it->title);
                $seo->setDescription(substr(strip_tags($it->excerpt ?: $it->content), 0, 160));
                $seo->setCanonical((string)($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/page?slug=' . rawurlencode($it->slug));
                $seo->addJsonLd([
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $it->title,
                    'dateModified' => $it->updated_at,
                ]);
            }
        } catch (\Throwable $e) {}

        $tpl = new Template(theme: 'default');
        $tpl->render('page_view.tpl', [
            'title' => $it->title,
            'content' => $it->content,
        ]);
    }
}
