{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">Rarog UI</h1>
    <p class="ce-muted">Официальный UI-слой и дизайн-система для CajeerEngine.</p>
  </div>

  <div class="ce-cards">
    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Единый UI-подход</div>
        <span class="ce-badge ce-badge--brand">ui</span>
      </div>
      <div class="ce-card__text">Rarog используется в админке и может использоваться в темах фронтенда для единообразного бренда.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Компоненты</div>
        <span class="ce-badge ce-badge--brand">components</span>
      </div>
      <div class="ce-card__text">Библиотека компонентов для Marketplace и UI Builder: блоки, сетки, карточки, формы.</div>
    </div>

    <div class="ce-card">
      <div class="ce-card__top">
        <div class="ce-card__title">Интеграция</div>
        <span class="ce-badge ce-badge--brand">assets</span>
      </div>
      <div class="ce-card__text">Подключение ассетов: <code>/assets/rarog/rarog.min.css</code>. Поверх можно накладывать theme.css.</div>
    </div>
  </div>

  <div class="ce-panel ce-mt-16">
    <div class="ce-panel__title">Практический смысл</div>
    <div class="ce-panel__list">
      <div class="ce-panel__item">
        <div class="ce-panel__name">Админка = продукт</div>
        <div class="ce-panel__desc">С UI Builder и Marketplace админка становится интерфейсом управления платформой, а не набором форм.</div>
      </div>
      <div class="ce-panel__item">
        <div class="ce-panel__name">Темы без хаоса</div>
        <div class="ce-panel__desc">Единые токены/компоненты — меньше “самописного CSS”, быстрее разработка тем.</div>
      </div>
    </div>
  </div>

  <div class="ce-actions ce-mt-16">
    <a class="ce-btn ce-btn--primary" href="/admin">Открыть админку</a>
    <a class="ce-btn ce-btn--ghost" href="/news">Обновления</a>
    <a class="ce-btn ce-btn--ghost" href="/docs">Документация</a>
  </div>
</section>

{include file="footer.tpl"}