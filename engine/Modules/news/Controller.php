<?php
namespace Modules\news;

use Content\Repository\ContentRepository;
use Template\Template;
use Support\ReleaseFeed;
use Support\MarkdownLite;
use Core\KernelSingleton;

class Controller
{
    public function index(): void
    {
        $repo = new ContentRepository();
        $items = $repo->list('news', ['page'=>max(1,(int)($_GET['page']??1)), 'per_page'=>10]);

        // Showcase mode: if DB has no news (or you want engine updates), build feed from CHANGELOG.md
        $releaseItems = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');
        if (!empty($releaseItems)) {
            $items = array_map(function($it){
                return [
                    'title' => $it['title'],
                    'slug' => $it['slug'],
                    'excerpt' => $it['excerpt'],
                    'published_at' => $it['version'],
                ];
            }, $releaseItems);
        }

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
        $releaseItems = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');
        $it = ReleaseFeed::findBySlug($releaseItems, $slug);

        // fallback to content repository
        $repo = new ContentRepository();
        $row = $repo->getBySlug('news', $slug);

        $tpl = new Template();
        if ($it) {
            $tpl->render('news_view.tpl', [
                'title' => $it['title'],
                'item_title' => $it['title'],
                'item_date' => 'v' . $it['version'],
                'item_body' => MarkdownLite::toHtml($it['body_md']),
            ]);
            return;
        }

        if (is_array($row)) {
            $tpl->render('news_view.tpl', [
                'title' => (string)($row['title'] ?? 'Новость'),
                'item_title' => (string)($row['title'] ?? ''),
                'item_date' => (string)($row['published_at'] ?? ''),
                'item_body' => (string)($row['body'] ?? ''),
            ]);
            return;
        }

        header('HTTP/1.1 404 Not Found');
        $tpl->render('error.tpl', ['title' => '404', 'message' => 'Новость не найдена']);
    }
}
