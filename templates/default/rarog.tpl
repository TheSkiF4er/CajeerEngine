{include file="header.tpl"}

<section class="ce-hero">
  <div class="ce-hero__left">
    <div class="ce-kicker">Rarog CSS • v4</div>
    <h1 class="ce-h1">Rarog CSS — гибридный CSS‑фреймворк нового поколения</h1>
    <p class="ce-lead">
      Дизайн‑токены, утилиты, компоненты и JS‑ядро без jQuery — в одном консистентном стеке.
      Сделано как полноценный продукт, а не набор разрозненных классов.
    </p>

    <div class="ce-actions">
      <a class="ce-btn ce-btn--primary ce-btn--lg" href="/docs?doc=docs%3ARAROG.md">Getting Started</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="https://github.com/TheSkiF4er/Rarog" target="_blank" rel="noopener">GitHub</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="https://github.com/TheSkiF4er/Rarog/releases" target="_blank" rel="noopener">Releases</a>
    </div>

    <div class="ce-card ce-card--flat ce-mt-16">
      <div class="ce-card__text ce-muted">
        Документация в движке: <a class="ce-link" href="/docs?doc=docs%3ARAROG.md">/docs → RAROG.md</a>
      </div>
    </div>
  </div>

  <div class="ce-hero__right">
    <div class="ce-panel">
      <div class="ce-panel__title">Быстрый старт</div>
      <div class="ce-panel__list">
        <div class="ce-panel__item">
          <div class="ce-panel__name">Подключение CSS</div>
          <div class="ce-panel__desc">Собранный файл + тема</div>
          <div class="ce-markdown ce-mt-8"><pre><code>&lt;link rel="stylesheet" href="/assets/rarog/rarog.min.css"&gt;
&lt;link rel="stylesheet" href="/assets/themes/default/theme.css"&gt;</code></pre></div>
        </div>
        <div class="ce-panel__item">
          <div class="ce-panel__name">Экосистема</div>
          <div class="ce-panel__desc">UI‑киты, стартеры, интеграции</div>
          <div class="ce-badges">
            <a class="ce-badge" href="https://github.com/TheSkiF4er/Rarog/tree/main/examples" target="_blank" rel="noopener">examples</a>
            <a class="ce-badge" href="https://github.com/TheSkiF4er/Rarog/tree/main/design" target="_blank" rel="noopener">design</a>
            <a class="ce-badge" href="https://github.com/TheSkiF4er/Rarog/tree/main/packages" target="_blank" rel="noopener">packages</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="ce-section">
  <div class="ce-section__head">
    <h2 class="ce-h2">Ключевые возможности</h2>
    <div class="ce-muted">Структура и тезисы повторяют официальный home‑экран проекта.</div>
  </div>

  <div class="ce-cards">
    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Дизайн‑токены</div>
        <span class="ce-chip">tokens</span>
      </div>
      <div class="ce-card__text ce-muted">Цвета, spacing, радиусы, тени и брейкпоинты вынесены в единый слой токенов.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Utility‑first</div>
        <span class="ce-chip">utils</span>
      </div>
      <div class="ce-card__text ce-muted">Утилитарные классы и responsive/state‑варианты в духе Tailwind.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Готовые компоненты</div>
        <span class="ce-chip">components</span>
      </div>
      <div class="ce-card__text ce-muted">Кнопки, карточки, сетка, nav, формы, alerts и многое другое.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">JS Core без jQuery</div>
        <span class="ce-chip">vanilla</span>
      </div>
      <div class="ce-card__text ce-muted">Dropdown, Collapse, Modal и другие компоненты на чистом ванильном JavaScript.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Темизация</div>
        <span class="ce-chip">themes</span>
      </div>
      <div class="ce-card__text ce-muted">Готовые темы (light/dark/contrast) и своя тема через CSS‑переменные.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">JIT / Tree‑shaking</div>
        <span class="ce-chip">build</span>
      </div>
      <div class="ce-card__text ce-muted">Сборка только реально используемых классов на основе контента проекта.</div>
    </div>
  </div>
</section>

<section class="ce-section">
  <div class="ce-section__head">
    <h2 class="ce-h2">README</h2>
    <div class="ce-muted">Ниже отображается README проекта (рендер Markdown).</div>
  </div>

  [if readme_html]
    <div class="ce-card">
      <div class="ce-markdown">{readme_html}</div>
    </div>
  [else]
    <div class="ce-card">
      <div class="ce-card__text ce-muted">
        README не удалось отрендерить в текущей сборке. Откройте документацию:
        <a class="ce-link" href="/docs?doc=docs%3ARAROG.md">RAROG.md</a>
      </div>
    </div>
  [/if]
</section>

{include file="footer.tpl"}
