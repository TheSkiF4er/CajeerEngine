{include file="header.tpl"}

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">Документация</h1>
    <p class="ce-muted">Все документы проекта (.md/.txt). Всего: {doc_total}.</p>
  </div>

  <div class="ce-docs__grid">
    <aside class="ce-docs__sidebar">
      <div class="ce-panel">
        <div class="ce-panel__title">Навигация</div>
        <div class="ce-panel__list ce-panel__list--dense">
          {toc_html}
        </div>
      </div>
    </aside>

    <div class="ce-docs__content">
      <div class="ce-article ce-markdown">
        <div class="ce-muted ce-text-sm">Файл: <b>{selected_label}</b></div>
        <div class="ce-mt-16">
          {content_html}
        </div>
      </div>
    </div>
  </div>
</section>

{include file="footer.tpl"}
