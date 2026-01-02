<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= \Template\DSL\Runtime::includeFile('header.tpl', $vars);
$__out .= '

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">Документация</h1>
    <p class="ce-muted">Все документы проекта (.md/.txt). Всего: ';
$__out .= \Template\DSL\Runtime::value('doc_total', $vars);
$__out .= '.</p>
  </div>

  <div class="ce-docs__grid">
    <aside class="ce-docs__sidebar">
      <div class="ce-panel">
        <div class="ce-panel__title">Навигация</div>
        <div class="ce-panel__list ce-panel__list--dense">
          ';
$__out .= \Template\DSL\Runtime::value('toc_html', $vars);
$__out .= '
        </div>
      </div>
    </aside>

    <div class="ce-docs__content">
      <div class="ce-article ce-markdown">
        <div class="ce-muted ce-text-sm">Файл: <b>';
$__out .= \Template\DSL\Runtime::value('selected_label', $vars);
$__out .= '</b></div>
        <div class="ce-mt-16">
          ';
$__out .= \Template\DSL\Runtime::value('content_html', $vars);
$__out .= '
        </div>
      </div>
    </div>
  </div>
</section>

';
$__out .= \Template\DSL\Runtime::includeFile('footer.tpl', $vars);
$__out .= '
';

echo $__out;
return $__out;
