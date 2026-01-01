{include file="header.tpl"}

<main class="rg-container rg-mt-3">
  <div class="rg-card">
    <div class="rg-card-header">
      <div class="rg-card-title">{item_title}</div>
      <div class="rg-card-subtitle">{item_date}</div>
    </div>
    <div class="rg-card-body rg-prose">
      {item_body}
      <div class="rg-mt-3">
        <a class="rg-btn rg-btn-secondary" href="/news">← Назад к обновлениям</a>
      </div>
    </div>
  </div>
</main>

{include file="footer.tpl"}
