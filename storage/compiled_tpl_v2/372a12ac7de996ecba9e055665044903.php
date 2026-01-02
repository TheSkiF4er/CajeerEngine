<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= \Template\DSL\Runtime::includeFile('header.tpl', $vars);
$__out .= '

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">Обновления движка</h1>
    <p class="ce-muted">Все обновления из CHANGELOG.md.</p>
  </div>

  <div class="ce-panel">
    <div class="ce-panel__list">
      ';
$__out .= \Template\DSL\Runtime::value('items_html', $vars);
$__out .= '
    </div>
  </div>
</section>

';
$__out .= \Template\DSL\Runtime::includeFile('footer.tpl', $vars);
$__out .= '
';

echo $__out;
return $__out;
