{include file="header.tpl"}

<section class="ce-hero">
  <div class="ce-hero__left">
    <div class="ce-kicker">Ecosystem</div>
    <h1 class="ce-h1">Marketplace</h1>
    <p class="ce-lead">
      Каталог расширений, тем и пакетов для CajeerEngine с проверкой подписи и контролем целостности.
    </p>

    <div class="ce-actions">
      <a class="ce-btn ce-btn--primary ce-btn--lg" href="/admin">Админ‑панель</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="/admin/marketplace/status">Статус</a>
      <a class="ce-btn ce-btn--ghost ce-btn--lg" href="/docs">Документация</a>
    </div>
  </div>

  <div class="ce-hero__right">
    <div class="ce-panel">
      <div class="ce-panel__title">Быстрый доступ</div>
      <div class="ce-panel__list">
        <a class="ce-panel__item" href="/admin/marketplace/themes">
          <div class="ce-panel__itemTitle">Темы</div>
          <div class="ce-muted ce-text-sm">Каталог и установка тем (admin).</div>
        </a>
        <a class="ce-panel__item" href="/admin/marketplace/plugins">
          <div class="ce-panel__itemTitle">Плагины</div>
          <div class="ce-muted ce-text-sm">Каталог и установка плагинов (admin).</div>
        </a>
      </div>
    </div>
  </div>
</section>

<section class="ce-section">
  <div class="ce-panel">
    <div class="ce-panel__title">О Marketplace</div>
    <div class="ce-markdown ce-mt-10">
      {content_html}
    </div>
  </div>

  <div class="ce-cards ce-mt-14">
    <div class="ce-card">
      <div class="ce-card__title">Пакеты</div>
      <div class="ce-muted ce-mt-8">Единый формат поставки, версионирование и предсказуемая установка.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__title">Обновления</div>
      <div class="ce-muted ce-mt-8">Контролируемое обновление и откат (rollback) через подсистему обновлений.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__title">Безопасность</div>
      <div class="ce-muted ce-mt-8">Проверка целостности и подписи (Ed25519) перед установкой.</div>
    </div>
  </div>
</section>

{include file="footer.tpl"}
