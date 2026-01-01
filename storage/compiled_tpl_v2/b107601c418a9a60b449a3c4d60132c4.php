<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= '<!doctype html>
<html lang="ru">
<head>
';
$__out .= \Template\DSL\Runtime::value('meta_tags', $vars);
$__out .= '
';
$__out .= \Template\DSL\Runtime::value('jsonld', $vars);
$__out .= '

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>';
$__out .= \Template\DSL\Runtime::value('title', $vars);
$__out .= '</title>
  <link rel="stylesheet" href="/assets/rarog/rarog.min.css?v=';
$__out .= \Template\DSL\Runtime::value('app_version', $vars);
$__out .= '">
  <link rel="stylesheet" href="/assets/themes/default/theme.css?v=';
$__out .= \Template\DSL\Runtime::value('app_version', $vars);
$__out .= '">
</head>
<body>
<header class="rg-container rg-mt-3">
  <h1 class="rg-title">';
$__out .= \Template\DSL\Runtime::value('title', $vars);
$__out .= '</h1>

  <nav class="rg-mt-2">
    <div class="rg-btn-group">
      <a class="rg-btn rg-btn-secondary" href="/">Главная</a>
      <a class="rg-btn rg-btn-secondary" href="/news">Обновления</a>
      <a class="rg-btn rg-btn-secondary" href="/docs">Docs</a>
      <a class="rg-btn rg-btn-secondary" href="/arog">Arog</a>
    </div>
  </nav>


  ';
if (\Template\DSL\Runtime::cond('logged', $vars)) {
$__out .= '
    <div class="rg-alert rg-alert-success">Вы вошли как: ';
$__out .= \Template\DSL\Runtime::value('user.name', $vars);
$__out .= '</div>
  ';
} else {
$__out .= '
    <div class="rg-alert rg-alert-warning">Гость: ';
$__out .= \Template\DSL\Runtime::value('user.name', $vars);
$__out .= '</div>
  ';
}
$__out .= '

  ';
if (\Template\DSL\Runtime::groupCheck('group', '1', $vars)) {
$__out .= '
    <div class="rg-alert rg-alert-info">Группа: Администратор</div>
  ';
}
$__out .= '

  ';
if (\Template\DSL\Runtime::groupCheck('not-group', '1', $vars)) {
$__out .= '
    <div class="rg-alert rg-alert-secondary">Нет прав администратора</div>
  ';
}
$__out .= '
</header>
';

echo $__out;
return $__out;
