<?php
namespace Modules\news;

use Template\Template;
use Support\ReleaseFeed;
use Support\MarkdownLite;

class Controller
{
    private function baseVars(string $title, string $desc = ''): array
    {
        $canonical = (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']))
            ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            : '';

        return [
            'seo_title' => $title . ' — CajeerEngine',
            'seo_description' => $desc,
            'seo_canonical' => $canonical,
            'seo_og' => '',
            'seo_twitter' => '',
            'head_extra' => '',
            'body_extra' => '',
        ];
    }

    public function index(): void
    {
        $releaseItems = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');

        $itemsHtml = '';
        foreach ($releaseItems as $it) {
            $title = htmlspecialchars((string)($it['title'] ?? ''), ENT_QUOTES, 'UTF-8');
            $excerpt = htmlspecialchars((string)($it['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8');
            $version = htmlspecialchars((string)($it['version'] ?? ''), ENT_QUOTES, 'UTF-8');
            $slug = rawurlencode((string)($it['slug'] ?? ''));

            $itemsHtml .= '<a class="ce-panel__item" href="/news/view?slug=' . $slug . '">';
            $itemsHtml .= '<div class="ce-panel__name">' . ($title !== '' ? $title : ('v' . $version)) . '</div>';
            if ($excerpt !== '') $itemsHtml .= '<div class="ce-panel__desc">' . $excerpt . '</div>';
            if ($version !== '') $itemsHtml .= '<div class="ce-badges"><span class="ce-badge ce-badge--brand">v' . $version . '</span></div>';
            $itemsHtml .= '</a>';
        }

        $tpl = new Template(theme: 'default');
        $tpl->render('news_list.tpl', array_merge(
            $this->baseVars('Обновления', 'Все обновления движка из CHANGELOG.md.'),
            [
                'items_html' => $itemsHtml,
            ]
        ));
    }

    public function view(): void
    {
        $slug = (string)($_GET['slug'] ?? '');
        $releaseItems = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');
        $it = ReleaseFeed::findBySlug($releaseItems, $slug);

        if (!$it) {
            header('HTTP/1.1 404 Not Found');
            $tpl = new Template(theme: 'default');
            $tpl->render('404.tpl', array_merge($this->baseVars('404'), [
                'title' => '404',
            ]));
            return;
        }

        $date = '';
        if (preg_match('/—\s*(\d{4}-\d{2}-\d{2})/u', (string)$it['title'], $m)) {
            $date = $m[1];
        }

        $tpl = new Template(theme: 'default');
        $tpl->render('news_view.tpl', array_merge(
            $this->baseVars((string)$it['title'], 'Детали релиза CajeerEngine.'),
            [
                'item_title' => (string)$it['title'],
                'item_date' => $date !== '' ? $date : ('v' . (string)$it['version']),
                'item_body' => MarkdownLite::toHtml((string)($it['body_md'] ?? '')),
            ]
        ));
    }
}
