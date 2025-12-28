{include file="header.tpl"}

<main class="rg-container rg-mt-3">
  <div class="rg-card rg-mb-2">
    <div class="rg-card-body">
      <div class="rg-btn-group">
        <a class="rg-btn rg-btn-primary" href="/news">Новости</a>
        <a class="rg-btn rg-btn-secondary" href="/page?slug=about">Страница: О проекте</a>
        <a class="rg-btn rg-btn-secondary" href="/category?slug=updates&type=news">Категория: Обновления</a>
      </div>
    </div>
  </div>

  <div class="rg-card">
    <div class="rg-card-body">
      {content}
      <div class="rg-mt-2">
        {module:news limit="5"}
      </div>
    </div>
  </div>
</main>

{include file="footer.tpl"}
