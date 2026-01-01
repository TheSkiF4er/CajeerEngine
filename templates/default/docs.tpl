{include file="header.tpl"}

<main class="rg-container rg-mt-3">
  <div class="rg-grid rg-grid-12 rg-gap-3">
    <aside class="rg-col-12 rg-col-md-4">
      <div class="rg-card">
        <div class="rg-card-header">
          <div class="rg-card-title">Документация проекта</div>
          <div class="rg-card-subtitle">Вся документация, которая присутствует в репозитории</div>
        </div>
        <div class="rg-card-body">
          <div class="rg-list">
            {toc_html}
          </div>

          <div class="rg-alert rg-alert-info rg-mt-2">
            Если /docs или /api возвращают 404, проверь rewrite (try_files) в nginx/aaPanel — это должен быть фронт-контроллер на /public/index.php.
          </div>
        </div>
      </div>
    </aside>

    <section class="rg-col-12 rg-col-md-8">
      <div class="rg-card">
        <div class="rg-card-body rg-prose">
          {content_html}
        </div>
      </div>
    </section>
  </div>
</main>

{include file="footer.tpl"}
