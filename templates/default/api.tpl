{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">API (Headless)</h1>
    <p class="ce-muted">Точки интеграции для SPA, мобильных приложений и серверных интеграций.</p>

    <div class="ce-actions ce-mt-16">
      <a class="ce-btn ce-btn--primary" href="/api/v1/ping">Ping</a>
      <a class="ce-btn ce-btn--ghost" href="/docs">Документация</a>
      <a class="ce-btn ce-btn--ghost" href="/news">Обновления</a>
    </div>
  </div>

  <div class="ce-panel">
    <div class="ce-panel__title">Быстрый старт</div>
    <div class="ce-panel__desc">Проверьте доступность API и получите примерные точки для интеграции.</div>

    <div class="ce-panel__grid">
      <div class="ce-panel__item">
        <div class="ce-panel__name">Проверка</div>
        <div class="ce-panel__desc"><code>GET /api/v1/ping</code></div>
        <div class="ce-badges"><span class="ce-badge ce-badge--ok">ready</span></div>
      </div>
      <div class="ce-panel__item">
        <div class="ce-panel__name">Авторизация</div>
        <div class="ce-panel__desc">Bearer token: <code>Authorization: Bearer &lt;token&gt;</code></div>
        <div class="ce-badges"><span class="ce-badge">scopes</span><span class="ce-badge">RBAC</span></div>
      </div>
      <div class="ce-panel__item">
        <div class="ce-panel__name">Наблюдаемость</div>
        <div class="ce-panel__desc"><code>/metrics</code>, <code>/api/v1/health/live</code>, <code>/api/v1/health/ready</code></div>
        <div class="ce-badges"><span class="ce-badge">ops</span></div>
      </div>
    </div>
  </div>

  <div class="ce-section__head ce-mt-16">
    <h2 class="ce-h2">Content API v1</h2>
    <p class="ce-muted">CRUD и выборки для контента.</p>
  </div>

  <div class="ce-cards">
    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Список</div>
        <span class="ce-badge">GET</span>
      </div>
      <div class="ce-card__text"><code>/api/v1/content</code></div>
      <div class="ce-badges"><span class="ce-badge">filters</span><span class="ce-badge">pagination</span></div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Получить</div>
        <span class="ce-badge">GET</span>
      </div>
      <div class="ce-card__text"><code>/api/v1/content/get?id=1</code></div>
      <div class="ce-badges"><span class="ce-badge">id</span></div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Создать</div>
        <span class="ce-badge">POST</span>
      </div>
      <div class="ce-card__text"><code>/api/v1/content/create</code></div>
      <div class="ce-badges"><span class="ce-badge">write</span><span class="ce-badge">draft</span></div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Обновить</div>
        <span class="ce-badge">POST</span>
      </div>
      <div class="ce-card__text"><code>/api/v1/content/update?id=1</code></div>
      <div class="ce-badges"><span class="ce-badge">write</span></div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Удалить</div>
        <span class="ce-badge">POST</span>
      </div>
      <div class="ce-card__text"><code>/api/v1/content/delete?id=1</code></div>
      <div class="ce-badges"><span class="ce-badge">danger</span></div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Публикация</div>
        <span class="ce-badge">POST</span>
      </div>
      <div class="ce-card__text"><code>/api/v1/content/publish?id=1</code></div>
      <div class="ce-badges"><span class="ce-badge">workflow</span></div>
    </div>
  </div>

  <div class="ce-card ce-card--flat ce-mt-16">
    <div class="ce-card__text ce-muted">Примечание: права доступа зависят от scopes и policy-aware RBAC.</div>
  </div>
</section>

{include file="footer.tpl"}
