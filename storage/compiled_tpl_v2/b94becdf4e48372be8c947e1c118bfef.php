<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= '</main>

<footer class="ce-footer">
  <div class="ce-container ce-footer__grid">
    <div>
      <div class="ce-footer__title">CajeerEngine</div>
      <div class="ce-muted ce-mt-8">Open-Source CMS-платформа (PHP + MySQL). Третье поколение (2025).</div>
      <div class="ce-muted ce-mt-8 ce-text-sm">Автор: TheSkiF4er • Лицензия: Apache-2.0</div>
    </div>

    <div>
      <div class="ce-footer__title">Разделы</div>
      <ul class="ce-list ce-mt-8">
        <li><a class="ce-link" href="/news">Обновления движка</a></li>
        <li><a class="ce-link" href="/docs">Документация</a></li>
        <li><a class="ce-link" href="/api/v1/ping">API status</a></li>
        <li><a class="ce-link" href="/admin">Админ-панель</a></li>
      </ul>
    </div>

    <div>
      <div class="ce-footer__title">Контакты</div>
      <ul class="ce-list ce-mt-8">
        <li class="ce-muted ce-text-sm">Support: <a class="ce-link" href="mailto:support@cajeer.ru">support@cajeer.ru</a></li>
        <li class="ce-muted ce-text-sm">Security: <a class="ce-link" href="mailto:security@cajeer.ru">security@cajeer.ru</a></li>
        <li class="ce-muted ce-text-sm">Telegram: <a class="ce-link" href="https://t.me/skif4er">@skif4er</a></li>
      </ul>
    </div>
  </div>

  <div class="ce-container ce-footer__bottom">
    <span>© ';
$__out .= \Template\DSL\Runtime::value('year', $vars);
$__out .= ' • CajeerEngine • ';
$__out .= \Template\DSL\Runtime::value('app_version', $vars);
$__out .= '</span>
    <span class="ce-chip">';
$__out .= \Template\DSL\Runtime::value('runtime_mode', $vars);
$__out .= '</span>
  </div>
</footer>

<script src="/assets/themes/default/theme.js"></script>
';
$__out .= \Template\DSL\Runtime::value('body_extra', $vars);
$__out .= '
</body>
</html>
';

echo $__out;
return $__out;
