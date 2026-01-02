<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= \Template\DSL\Runtime::includeFile('header.tpl', $vars);
$__out .= '

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">Rarog</h1>
    <p class="ce-muted">Официальный UI‑слой CajeerEngine: tokens, utilities, компоненты и JS‑ядро.</p>
  </div>

  <div class="ce-cards">
    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Rarog Portal</div>
        <span class="ce-badge ce-badge--brand">Web</span>
      </div>
      <div class="ce-card__text">
        Отдельный домен с документацией и витриной компонентов.
      </div>
      <div class="ce-mt-8">
        <a class="ce-btn ce-btn--primary" href="https://rarog.cajeer.ru" target="_blank" rel="noopener noreferrer">Открыть rarog.cajeer.ru</a>
      </div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Репозиторий</div>
        <span class="ce-badge ce-badge--brand">GitHub</span>
      </div>
      <div class="ce-card__text">
        Исходники, релизы, примеры, документация и плагины.
      </div>
      <div class="ce-mt-8">
        <a class="ce-btn ce-btn--ghost" href="https://github.com/TheSkiF4er/Rarog" target="_blank" rel="noopener noreferrer">Открыть репозиторий</a>
      </div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Интеграция</div>
        <span class="ce-badge">CajeerEngine</span>
      </div>
      <div class="ce-card__text">
        Слой темы отвечает за фирменные токены и витрину платформы.
      </div>
      <div class="ce-mt-8">
        <a class="ce-btn ce-btn--ghost" href="/docs?doc=docs%3ARAROG_RU.md">Открыть заметку</a>
      </div>
    </div>
  </div>

  <div class="ce-article ce-markdown ce-mt-16">
    ';
$__out .= \Template\DSL\Runtime::value('rarog_doc_html', $vars);
$__out .= '
  </div>
</section>

';
$__out .= \Template\DSL\Runtime::includeFile('footer.tpl', $vars);
$__out .= '
';

echo $__out;
return $__out;
