<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= \Template\DSL\Runtime::includeFile('header.tpl', $vars);
$__out .= '
<section class="ce-section ce-center">
  <div class="ce-notfound">
    <div class="ce-notfound__code">404</div>
    <div class="ce-notfound__title">Страница не найдена</div>
    <div class="ce-muted ce-mt-8">Похоже, вы перешли по неверной ссылке.</div>
    <div class="ce-actions ce-mt-16">
      <a class="ce-btn ce-btn--primary" href="/">На главную</a>
      <a class="ce-btn ce-btn--ghost" href="/news">Обновления</a>
      <a class="ce-btn ce-btn--ghost" href="/admin">Админка</a>
    </div>
  </div>
</section>
';
$__out .= \Template\DSL\Runtime::includeFile('footer.tpl', $vars);

echo $__out;
return $__out;
