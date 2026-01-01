{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">API (Headless)</h1>
    <p class="ce-muted">Точки интеграции для SPA, мобильных приложений и SSG.</p>
  </div>

  <div class="ce-article">
    <h3>Проверка</h3>
    <p><code>GET /api/v1/ping</code></p>

    <h3>Content</h3>
    <ul>
      <li><code>GET /api/v1/content</code></li>
      <li><code>GET /api/v1/content/get</code></li>
      <li><code>POST /api/v1/content/create</code></li>
      <li><code>POST /api/v1/content/update</code></li>
      <li><code>POST /api/v1/content/delete</code></li>
      <li><code>POST /api/v1/content/publish</code></li>
    </ul>

    <h3>UI Builder / Marketplace</h3>
    <ul>
      <li><code>GET /api/v1/ui/blocks</code>, <code>/api/v1/ui/get</code>, <code>/api/v1/ui/preview</code>, <code>POST /api/v1/ui/save</code></li>
      <li><code>GET /api/v1/marketplace/index</code>, <code>/api/v1/marketplace/installed</code>, <code>POST /api/v1/marketplace/upload-install</code></li>
    </ul>

    <h3>Health / Metrics</h3>
    <ul>
      <li><code>GET /api/v1/health/live</code>, <code>/api/v1/health/ready</code></li>
      <li><code>GET /metrics</code></li>
    </ul>

    <p class="ce-muted ce-text-sm">Примечание: права доступа зависят от scopes и policy-aware RBAC.</p>
  </div>

  <div class="ce-actions ce-mt-16">
    <a class="ce-btn ce-btn--primary" href="/api/v1/ping">Ping →</a>
    <a class="ce-btn ce-btn--ghost" href="/docs">Документация</a>
    <a class="ce-btn ce-btn--ghost" href="/admin">Админка</a>
  </div>
</section>

{include file="footer.tpl"}