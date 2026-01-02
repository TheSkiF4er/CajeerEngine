<?php
namespace Modules\home;

use Template\Template;
use Support\MarkdownLite;
use Support\ReleaseFeed;

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
            'runtime_mode' => 'web',
        ];
    }

    public function index(): void
    {
        $historyPath = ROOT_PATH . '/docs/HISTORY_RU.md';
        $historyMd = is_file($historyPath) ? (string)@file_get_contents($historyPath) : '';

        $releases = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');
        $releases = array_slice($releases, 0, 12); // latest 12

        $releasesHtml = '';
        foreach ($releases as $it) {
            $title = htmlspecialchars((string)($it['title'] ?? ($it['version'] ?? 'Release')), ENT_QUOTES, 'UTF-8');
            $excerpt = htmlspecialchars((string)($it['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8');
            $slug = rawurlencode((string)($it['slug'] ?? ''));
            $badge = htmlspecialchars((string)($it['version'] ?? ''), ENT_QUOTES, 'UTF-8');

            $releasesHtml .= '<a class="ce-panel__item" href="/news/view?slug=' . $slug . '">';
            $releasesHtml .= '<div class="ce-panel__name">' . $title . '</div>';
            if ($excerpt !== '') $releasesHtml .= '<div class="ce-panel__desc">' . $excerpt . '</div>';
            if ($badge !== '') $releasesHtml .= '<div class="ce-badges"><span class="ce-badge ce-badge--brand">' . $badge . '</span></div>';
            $releasesHtml .= '</a>';
        }

        $tpl = new Template(theme: 'default');
        $tpl->render('home.tpl', array_merge(
            $this->baseVars('Главная', 'История и обзор CajeerEngine: релизы, архитектура и ключевые возможности.'),
            [
                'history_html' => MarkdownLite::toHtml($historyMd),
                'releases_html' => $releasesHtml,
            ]
        ));
    }
}
