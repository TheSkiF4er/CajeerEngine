{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">API</h1>
    <p class="ce-muted">Публичный Content API v1 и внутренняя карта /api для модулей платформы.</p>
  </div>

  <div class="ce-cards">
    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Content API v1</div>
        <span class="ce-badge ce-badge--brand">/api/v1/*</span>
      </div>
      <div class="ce-card__text">
        Рекомендуемый путь для headless‑интеграций и внешних клиентов. Авторизация: <code>Authorization: Bearer &lt;token&gt;</code>.
      </div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Internal API</div>
        <span class="ce-badge">{api_internal_enabled}</span>
      </div>
      <div class="ce-card__text">
        Внутренняя карта эндпоинтов из <code>system/api.php</code>. Токенов в конфиге: <b>{api_tokens_count}</b>.
      </div>
    </div>
  </div>

  <div class="ce-api__grid ce-mt-16">
    <div class="ce-panel">
      <div class="ce-panel__title">Endpoints: /api/v1</div>
      <div class="ce-panel__list ce-api__list">
        {api_v1_html}
      </div>
    </div>

    <div class="ce-panel">
      <div class="ce-panel__title">Endpoints: /api</div>
      <div class="ce-panel__list ce-api__list">
        {api_internal_html}
      </div>
    </div>
  </div>

  <div class="ce-article ce-markdown ce-mt-16">
    {api_docs_html}
  </div>
</section>

{include file="footer.tpl"}
