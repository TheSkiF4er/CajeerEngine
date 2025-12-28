{include file="header.tpl"}

<main class="rg-container rg-mt-3">
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
