{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">{item_title}</h1>
    <p class="ce-muted">{item_date}</p>
  </div>

  <div class="ce-article ce-markdown">
    {item_body}

    <div class="ce-mt-16">
      <a class="ce-btn ce-btn--ghost" href="/news">← Назад к обновлениям</a>
    </div>
  </div>
</section>

{include file="footer.tpl"}
