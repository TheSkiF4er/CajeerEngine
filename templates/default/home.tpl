{include file="header.tpl"}

<section class="ce-hero">
  <div class="ce-hero__left">
    <div class="ce-kicker">Platform • {app_version}</div>
    <h1 class="ce-h1">CajeerEngine — независимая CMS‑платформа, а не «шаблонный движок»</h1>
    <p class="ce-lead">
      DLE‑совместимые .tpl + расширенный DSL, headless‑API, marketplace, multi‑tenant и enterprise‑контроль.
      Всё внутри ядра — без сторонних шаблонизаторов и без «готовых решений».
    </p>

    <div class="ce-actions">
      <a class="ce-btn ce-btn--primary ce-btn--lg" href="/news">Обновления движка</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="/admin">Открыть админку</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="/api/v1/ping">API status</a>
    </div>

    <div class="ce-metrics">
      <div class="ce-metric">
        <div class="ce-metric__label">Режим</div>
        <div class="ce-metric__value">{runtime_mode}</div>
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
    <div class="ce-panel">
      <div class="ce-panel__title">Что демонстрирует дефолтный сайт</div>

      <div class="ce-panel__list">
        <div class="ce-panel__item">
          <div class="ce-panel__name">Content API</div>
          <div class="ce-panel__desc">CRUD, фильтры, пагинация, версии (draft/published)</div>
          <div class="ce-badges"><span class="ce-badge ce-badge--ok">ready</span><span class="ce-badge">scopes</span></div>
        </div>
        <div class="ce-panel__item">
          <div class="ce-panel__name">UI Builder</div>
          <div class="ce-panel__desc">JSON → render pipeline, preview mode</div>
          <div class="ce-badges"><span class="ce-badge ce-badge--warn">beta</span><span class="ce-badge">blocks</span></div>
        </div>
        <div class="ce-panel__item">
          <div class="ce-panel__name">Marketplace</div>
          <div class="ce-panel__desc">Плагины, UI‑блоки, типы контента, обновления</div>
          <div class="ce-badges"><span class="ce-badge ce-badge--brand">ecosystem</span><span class="ce-badge">signing</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="ce-section">
  <div class="ce-section__head">
    <h2 class="ce-h2">Преимущества платформы</h2>
    <p class="ce-muted">Ключевые блоки CajeerEngine — как продукт, а не набор скриптов.</p>
  </div>

  <div class="ce-cards">
    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">DLE‑совместимые .tpl</div>
        <span class="ce-badge ce-badge--brand">tpl</span>
      </div>
      <div class="ce-card__text">Миграция шаблонов без переписывания: include, if/else, group, available.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Headless‑режим</div>
        <span class="ce-badge ce-badge--brand">api</span>
      </div>
      <div class="ce-card__text">API-first для SPA, мобильных приложений, SSG и SaaS. Scopes + policy‑aware RBAC.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Плагины & события</div>
        <span class="ce-badge ce-badge--brand">ext</span>
      </div>
      <div class="ce-card__text">Расширяй CMS безопасно: hooks/events, lifecycle модулей, зависимости, overrides.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Multi‑site / Multi‑tenant</div>
        <span class="ce-badge ce-badge--brand">saas</span>
      </div>
      <div class="ce-card__text">Один core → много сайтов/клиентов. Изоляция, квоты, политики и контроль доступа.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Updater & Packages</div>
        <span class="ce-badge ce-badge--brand">ops</span>
      </div>
      <div class="ce-card__text">Безопасные обновления и откат: .cajeerpkg/.cajeerpatch, бэкапы, каналы stable/beta.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Enterprise Security</div>
        <span class="ce-badge ce-badge--brand">sec</span>
      </div>
      <div class="ce-card__text">Audit logs, rate limiting, CSRF/XSS, Zero Trust foundation, строгая политика доступа.</div>
    </div>
  </div>
</section>

<section class="ce-section">
  <div class="ce-section__head">
    <h2 class="ce-h2">Обновления движка</h2>
    <p class="ce-muted">Новости на сайте — это релизы CajeerEngine. Никакого «блога ради блога».</p>
  </div>

  <div class="ce-news">
    {module:news limit=6 format=updates}
  </div>

  <div class="ce-center ce-mt-16">
    <a class="ce-btn ce-btn--ghost" href="/news">Показать все обновления →</a>
  </div>
</section>

{include file="footer.tpl"}
