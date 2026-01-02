<?php
declare(strict_types=1);

namespace Modules\home;

use Template\Template;
use Support\ReleaseFeed;
use Support\MarkdownLite;

final class Controller
{
    private function baseVars(string $title, string $desc = ''): array
    {
        $version = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt')) ?: '0.0.0';
        $canonical = (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']))
            ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            : '';

        return [
            'seo_title' => $title . ' — CajeerEngine',
            'title' => $title . ' — CajeerEngine',
            'description' => $desc,
            'canonical' => $canonical,
            'app_version' => $version,
            'year' => date('Y'),
        ];
    }

    public function index(): void
    {
        $vars = $this->baseVars(
            'CajeerEngine',
            'Open-Source CMS-платформа нового поколения: headless-API, marketplace, multi-tenant, enterprise.'
        );

        // Latest releases (reuse /news style)
        $releases = ReleaseFeed::fromChangelog(ROOT_PATH . '/CHANGELOG.md');
        $items = [];
        if (!empty($releases)) {
            $items = array_map(static function(array $it){
                return [
                    'title' => (string)($it['title'] ?? ('v' . ($it['version'] ?? ''))),
                    'slug' => (string)($it['slug'] ?? ''),
                    'excerpt' => (string)($it['excerpt'] ?? ''),
                    'published_at' => 'v' . (string)($it['version'] ?? ''),
                ];
            }, array_slice($releases, 0, 4));
        }

        $releasesHtml = '';
        try {
            // Prefer the same renderer as /news
            if (class_exists('\\Content\\View') && method_exists('\\Content\\View', 'renderList')) {
                $releasesHtml = \Content\View::renderList($items);
            } else {
                $releasesHtml = $this->renderSimpleReleaseCards($releases);
            }
        } catch (\Throwable $e) {
            $releasesHtml = $this->renderSimpleReleaseCards($releases);
        }

        // Better “История проекта” (HTML cards)
        $historyHtml = $this->buildHistoryHtml();

        // Provide multiple keys for compatibility with different home.tpl placeholders
        $vars['releases_html'] = $releasesHtml;
        $vars['latest_releases_html'] = $releasesHtml;
        $vars['news_html'] = $releasesHtml;

        $vars['history_html'] = $historyHtml;
        $vars['project_history_html'] = $historyHtml;
        $vars['project_history'] = $historyHtml;

        $tpl = new Template(theme: 'default');
        $tpl->render('home.tpl', $vars);
    }

    private function renderSimpleReleaseCards(array $releases): string
    {
        if (empty($releases)) {
            return '<div class="ce-card ce-card--flat"><div class="ce-card__text ce-muted">Пока нет данных о релизах. Проверьте CHANGELOG.md.</div></div>';
        }

        $html = '<div class="ce-cards">';
        foreach (array_slice($releases, 0, 4) as $it) {
            $title = htmlspecialchars((string)($it['title'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $ver = htmlspecialchars((string)($it['version'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $excerpt = (string)($it['excerpt'] ?? '');
            $excerpt = $excerpt !== '' ? MarkdownLite::toHtml($excerpt) : '<span class="ce-muted">Описание не задано.</span>';
            $slug = htmlspecialchars((string)($it['slug'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $html .= '<div class="ce-card">';
            $html .= '  <div class="ce-card__top">';
            $html .= '    <div class="ce-card__title">'.($title !== '' ? $title : ('v'.$ver)).'</div>';
            $html .= '    <span class="ce-badge">v'.$ver.'</span>';
            $html .= '  </div>';
            $html .= '  <div class="ce-card__text">'.$excerpt.'</div>';
            $html .= '  <div class="ce-actions ce-mt-16"><a class="ce-btn ce-btn--ghost" href="/news/view?slug='.$slug.'">Подробнее</a></div>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    private function buildHistoryHtml(): string
    {
        return '
<div class="ce-cards">
  <div class="ce-card">
    <div class="ce-card__top">
      <div class="ce-card__title">Начало</div>
      <span class="ce-badge">foundation</span>
    </div>
    <div class="ce-card__text ce-muted">Проект начинался как внутренний инструмент: шаблоны, контент и быстрая публикация без “комбайнов”.</div>
  </div>

  <div class="ce-card">
    <div class="ce-card__top">
      <div class="ce-card__title">DLE‑совместимость</div>
      <span class="ce-badge">tpl</span>
    </div>
    <div class="ce-card__text ce-muted">Ставка на понятные .tpl‑шаблоны + DSL: контроль над фронтом без сторонних шаблонизаторов.</div>
  </div>

  <div class="ce-card">
    <div class="ce-card__top">
      <div class="ce-card__title">Платформа</div>
      <span class="ce-badge">core+modules</span>
    </div>
    <div class="ce-card__text ce-muted">Переход к архитектуре “ядро + модули + документация + релизы”, ориентированной на реальный продукт.</div>
  </div>

  <div class="ce-card">
    <div class="ce-card__top">
      <div class="ce-card__title">Headless‑first</div>
      <span class="ce-badge">api</span>
    </div>
    <div class="ce-card__text ce-muted">Content API v1: CRUD, фильтры, пагинация, версии (draft/published) — удобно для SPA и мобильных клиентов.</div>
  </div>

  <div class="ce-card">
    <div class="ce-card__top">
      <div class="ce-card__title">Marketplace</div>
      <span class="ce-badge">ecosystem</span>
    </div>
    <div class="ce-card__text ce-muted">Витрина тем и плагинов как часть экосистемы: установка, обновления, доверие к поставщикам.</div>
  </div>

  <div class="ce-card">
    <div class="ce-card__top">
      <div class="ce-card__title">Третье поколение</div>
      <span class="ce-badge ce-badge--brand">2025+</span>
    </div>
    <div class="ce-card__text ce-muted">Multi‑tenant и enterprise‑контроль, наблюдаемость и безопасность как базовые свойства платформы.</div>
  </div>
</div>';
    }
}
