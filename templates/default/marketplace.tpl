{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">{title}</h1>
    <p class="ce-muted">{subtitle}</p>

    <div class="ce-actions ce-mt-16">
      <a class="ce-btn ce-btn--primary" href="/marketplace/themes">Темы</a>
      <a class="ce-btn ce-btn--ghost" href="/marketplace/plugins">Плагины и модули</a>
      <a class="ce-btn ce-btn--ghost" href="/marketplace/profile">Загрузить</a>
    </div>
  </div>

  <div class="ce-panel">
    <div class="ce-panel__title">Что такое Marketplace</div>
    <div class="ce-panel__desc">Публичная витрина ресурсов CajeerEngine. Здесь вы выбираете темы и расширения, а управление публикацией — в профиле.</div>

    <div class="ce-panel__grid">
      <div class="ce-panel__item">
        <div class="ce-panel__name">Темы</div>
        <div class="ce-panel__desc">Оформление и UI‑пакеты.</div>
        <div class="ce-badges"><span class="ce-badge">theme</span></div>
      </div>
      <div class="ce-panel__item">
        <div class="ce-panel__name">Плагины</div>
        <div class="ce-panel__desc">Функциональные расширения.</div>
        <div class="ce-badges"><span class="ce-badge">plugin</span></div>
      </div>
      <div class="ce-panel__item">
        <div class="ce-panel__name">Модули</div>
        <div class="ce-panel__desc">Крупные подсистемы.</div>
        <div class="ce-badges"><span class="ce-badge">module</span></div>
      </div>
    </div>
  </div>

  <div class="ce-section__head ce-mt-16">
    <h2 class="ce-h2">Рекомендуемые темы</h2>
    <p class="ce-muted">Выдержка из локального индекса Marketplace.</p>
  </div>
  {themes_html}

  <div class="ce-actions ce-mt-16">
    <a class="ce-btn ce-btn--ghost" href="/marketplace/themes">Показать все темы</a>
  </div>

  <div class="ce-section__head ce-mt-16">
    <h2 class="ce-h2">Рекомендуемые расширения</h2>
    <p class="ce-muted">Плагины и модули из локального индекса Marketplace.</p>
  </div>
  {plugins_html}

  <div class="ce-actions ce-mt-16">
    <a class="ce-btn ce-btn--ghost" href="/marketplace/plugins">Показать все расширения</a>
    <a class="ce-btn ce-btn--primary" href="/marketplace/profile">Загрузить ресурс</a>
  </div>

  <div class="ce-card ce-card--flat ce-mt-16">
    <div class="ce-card__text ce-muted">
      В этой сборке витрина читает локальный индекс. Следующий шаг — подключение реестра и пользовательских аккаунтов.
    </div>
  </div>
</section>

{include file="footer.tpl"}
