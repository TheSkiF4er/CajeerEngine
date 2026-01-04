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
        // Prefer canonical project history file.
        // (docs/HISTORY.md is the source of truth; *_RU is optional fallback.)
        $historyPath = ROOT_PATH . '/docs/HISTORY.md';
        if (!is_file($historyPath)) {
            $historyPath = ROOT_PATH . '/docs/HISTORY_RU.md';
        }
        $historyMd = is_file($historyPath) ? (string)@file_get_contents($historyPath) : '';

        $releases = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');
        $releases = array_slice($releases, 0, 12); // latest 12

        $releasesHtml = '';
        foreach ($releases as $it) {
            $title = htmlspecialchars((string)($it['title'] ?? ($it['version'] ?? 'Релиз')), ENT_QUOTES, 'UTF-8');

            // Make excerpt human-friendly for homepage (first non-empty line, without md markers).
            $excerptMd = (string)($it['excerpt'] ?? '');
            $excerptLine = '';
            foreach (preg_split('/\R/', trim($excerptMd)) ?: [] as $ln) {
                $ln = trim($ln);
                if ($ln === '') continue;
                $ln = preg_replace('/^#{1,6}\s+/', '', $ln) ?? $ln;
                $ln = preg_replace('/^[-*+]\s+/', '', $ln) ?? $ln;
                $ln = preg_replace('/^\d+\.\s+/', '', $ln) ?? $ln;
                $excerptLine = $ln;
                break;
            }
            $excerpt = htmlspecialchars($excerptLine, ENT_QUOTES, 'UTF-8');

            $slug = rawurlencode((string)($it['slug'] ?? ''));
            $badge = htmlspecialchars((string)($it['version'] ?? ''), ENT_QUOTES, 'UTF-8');

            $releasesHtml .= '<a class="ce-panel__item" href="/news/view?slug=' . $slug . '">';
            $releasesHtml .= '<div class="ce-panel__name">' . $title . '</div>';
            if ($excerpt !== '') $releasesHtml .= '<div class="ce-panel__desc">' . $excerpt . '</div>';
            if ($badge !== '') $releasesHtml .= '<div class="ce-badges"><span class="ce-badge ce-badge--brand">' . $badge . '</span></div>';
            $releasesHtml .= '</a>';
        }

        if ($releasesHtml === '') {
            $releasesHtml = '<div class="ce-panel__item">'
                . '<div class="ce-panel__name">Нет релизов</div>'
                . '<div class="ce-panel__desc">CHANGELOG.md не найден или не содержит версионированных заголовков (## 1.2.3).</div>'
                . '</div>';
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
