<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= \Template\DSL\Runtime::includeFile('header.tpl', $vars);
$__out .= '

<section class="ce-section">
  <div class="ce-section__head">
    <h1 class="ce-h2">Обновления движка</h1>
    <p class="ce-muted">Список релизов и ключевых изменений.</p>
  </div>

  <div class="ce-card ce-card--flat">
    ';
$__out .= \Template\DSL\Runtime::value('items_html', $vars);
$__out .= '
  </div>
</section>

';
$__out .= \Template\DSL\Runtime::includeFile('footer.tpl', $vars);

echo $__out;
return $__out;
