{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">{title}</h1>
    <p class="ce-muted">{subtitle}</p>

    <div class="ce-actions ce-mt-16">
      <a class="ce-btn ce-btn--ghost" href="/marketplace">Витрина</a>
      <a class="ce-btn ce-btn--ghost" href="/marketplace/themes">Темы</a>
      <a class="ce-btn ce-btn--ghost" href="/marketplace/plugins">Плагины и модули</a>
      <a class="ce-btn ce-btn--primary" href="/marketplace/profile">Загрузить</a>
    </div>
  </div>

  {items_html}
</section>

{include file="footer.tpl"}
