{include file="header.tpl"}

<main class="rg-container rg-mt-3">
  <div class="rg-card rg-mb-2">
    <div class="rg-card-body">
      <form method="get" action="/news">
        <input class="rg-input" type="text" name="q" value="{q}" placeholder="Поиск по новостям">
        <input type="hidden" name="cat" value="{cat}">
        <div class="rg-mt-2">
          <button class="rg-btn rg-btn-primary" type="submit">Искать</button>
          <a class="rg-btn rg-btn-secondary" href="/news">Сброс</a>
        </div>
      </form>
    </div>
  </div>

  {items_html}
  {pagination_html}
</main>

{include file="footer.tpl"}
