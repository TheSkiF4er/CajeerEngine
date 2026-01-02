<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= \Template\DSL\Runtime::includeFile('header.tpl', $vars);
$__out .= '

<section class="ce-hero">
  <div class="ce-hero__left">
    <div class="ce-kicker">Platform • ';
$__out .= \Template\DSL\Runtime::value('app_version', $vars);
$__out .= '</div>
    <h1 class="ce-h1">CajeerEngine — независимая CMS‑платформа, а не «шаблонный движок»</h1>
    <p class="ce-lead">
      DLE‑совместимые .tpl + расширенный DSL, headless‑API, marketplace, multi‑tenant и enterprise‑контроль.
      Всё внутри ядра — без сторонних шаблонизаторов и без «готовых решений».
    </p>

    <div class="ce-actions">
      <a class="ce-btn ce-btn--primary ce-btn--lg" href="/news">Обновления</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="/rarog">Rarog</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="/api">API</a>
    </div>

    <div class="ce-metrics">
      <div class="ce-metric">
        <div class="ce-metric__label">Режим</div>
        <div class="ce-metric__value">';
$__out .= \Template\DSL\Runtime::value('runtime_mode', $vars);
$__out .= '</div>
      </div>
      <div class="ce-metric">
        <div class="ce-metric__label">Шаблоны</div>
        <div class="ce-metric__value">DLE‑style .tpl</div>
      </div>
      <div class="ce-metric">
        <div class="ce-metric__label">API</div>
        <div class="ce-metric__value">Headless‑first</div>
      </div>
    </div>
  </div>

  <div class="ce-hero__right">
    <div class="ce-panel ce-panel--ghost">
      <div class="ce-panel__title">Что демонстрирует дефолтный сайт</div>

      <div class="ce-panel__list">
        <div class="ce-panel__item">
          <div class="ce-panel__name">Content API</div>
          <div class="ce-panel__desc">CRUD, фильтры, пагинация, версии (draft/published). Удобно для SPA и мобильных приложений.</div>
          <div class="ce-badges"><span class="ce-badge ce-badge--brand">/api</span></div>
        </div>

        <div class="ce-panel__item">
          <div class="ce-panel__name">Документация</div>
          <div class="ce-panel__desc">Все .md/.txt документы из репозитория доступны на /docs с удобной навигацией.</div>
          <div class="ce-badges"><span class="ce-badge ce-badge--brand">/docs</span></div>
        </div>

        <div class="ce-panel__item">
          <div class="ce-panel__name">Релизы</div>
          <div class="ce-panel__desc">Лента обновлений строится из CHANGELOG.md, чтобы витрина всегда совпадала с версией движка.</div>
          <div class="ce-badges"><span class="ce-badge ce-badge--brand">/news</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="ce-section">
  <div class="ce-section__head">
    <h2 class="ce-h2">История проекта</h2>
    <p class="ce-muted">От внутреннего инструмента до третьего поколения open‑source платформы.</p>
  </div>
  <div class="ce-article ce-markdown">
    ';
$__out .= \Template\DSL\Runtime::value('history_html', $vars);
$__out .= '
  </div>
</section>

<section class="ce-section">
  <div class="ce-section__head">
    <h2 class="ce-h2">Последние релизы</h2>
    <p class="ce-muted">Короткая выжимка из CHANGELOG.md.</p>
  </div>

  <div class="ce-panel ce-panel--ghost">
    <div class="ce-panel__list">
      ';
$__out .= \Template\DSL\Runtime::value('releases_html', $vars);
$__out .= '
    </div>
  </div>
</section>

';
$__out .= \Template\DSL\Runtime::includeFile('footer.tpl', $vars);
$__out .= '
';

echo $__out;
return $__out;
