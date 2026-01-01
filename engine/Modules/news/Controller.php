<?php
namespace Modules\news;

use Content\Repository\ContentRepository;
use Template\Template;
use Core\KernelSingleton;

class Controller
{
    public function index(): void
    {
        $repo = new ContentRepository();
        $items = $repo->list('news', ['page'=>max(1,(int)($_GET['page']??1)), 'per_page'=>10]);

        // SEO
        try {
            $seo = KernelSingleton::container()->get('seo');
            if ($seo instanceof \Seo\MetaManager) {
                $seo->setTitle('Новости');
                $seo->setCanonical((string)($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/news');
            }
        } catch (\Throwable $e) {}

        $tpl = new Template(theme: 'default');
        $tpl->render('news_list.tpl', [
            'title' => 'Новости',
            'items_html' => \Content\View::renderList($items),
        ]);
    }

    public function view(): void
    {
        $slug = (string)($_GET['slug'] ?? '');
        $repo = new ContentRepository();
        $it = $repo->findBySlug('news', $slug);
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
                $seo->setCanonical((string)($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/news/view?slug=' . rawurlencode($it->slug));
                $seo->addOg('og:type', 'article');
                $seo->addJsonLd([
                    '@context' => 'https://schema.org',
                    '@type' => 'NewsArticle',
                    'headline' => $it->title,
                    'datePublished' => $it->created_at,
                    'dateModified' => $it->updated_at,
                ]);
            }
        } catch (\Throwable $e) {}

        $tpl = new Template(theme: 'default');
        $tpl->render('news_view.tpl', [
            'title' => $it->title,
            'content' => $it->content,
        ]);
    }
}
