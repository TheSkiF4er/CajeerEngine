<?php
// Compiled by CajeerEngine Template DSL
$__out = '';
$vars = $vars ?? [];

$__out .= '<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>';
$__out .= \Template\DSL\Runtime::value('seo_title', $vars);
$__out .= '</title>
  <meta name="description" content="';
$__out .= \Template\DSL\Runtime::value('seo_description', $vars);
$__out .= '">
  <link rel="canonical" href="';
$__out .= \Template\DSL\Runtime::value('seo_canonical', $vars);
$__out .= '">
  ';
$__out .= \Template\DSL\Runtime::value('seo_og', $vars);
$__out .= '
  ';
$__out .= \Template\DSL\Runtime::value('seo_twitter', $vars);
$__out .= '

  <!-- Rarog (optional, if installed) -->
  <link rel="stylesheet" href="/assets/rarog/rarog.min.css?v=';
$__out .= \Template\DSL\Runtime::value('app_version', $vars);
$__out .= '">
  <!-- CajeerEngine theme -->
  <link rel="stylesheet" href="/assets/themes/default/theme.css?v=';
$__out .= \Template\DSL\Runtime::value('app_version', $vars);
$__out .= '">
  ';
$__out .= \Template\DSL\Runtime::value('head_extra', $vars);
$__out .= '
</head>
<body class="ce-body">
<div class="ce-bg"></div>
<header class="ce-header">
  <div class="ce-container ce-header__row">
    <a class="ce-brand" href="/">
      <span class="ce-brand__mark">CE</span>
      <span class="ce-brand__name">CajeerEngine</span>
      <span class="ce-brand__tag">CMS-платформа нового поколения</span>
    </a>

    <nav class="ce-nav">
      <a class="ce-nav__link" href="/">Главная</a>
      <a class="ce-nav__link" href="/news">Обновления</a>
      <a class="ce-nav__link" href="/docs">Документация</a>
      <a class="ce-nav__link" href="/api">API</a>
      <a class="ce-nav__link" href="https://discord.gg/E52DxpShQy" target="_blank" rel="noopener noreferrer">Сообщество</a>
      <a class="ce-nav__link" href="/marketplace">Ресурсы</a>
      <a class="ce-btn ce-btn--primary" href="/rarog">Rarog</a>
      <button class="ce-burger" data-ce="burger" aria-label="Меню">
        <span></span><span></span><span></span>
      </button>
    </nav>
  </div>

  <div class="ce-mobile" data-ce="mobile">
    <div class="ce-container ce-mobile__grid">
      <a class="ce-mobile__link" href="/">Главная</a>
      <a class="ce-mobile__link" href="/news">Обновления</a>
      <a class="ce-mobile__link" href="/docs">Документация</a>
      <a class="ce-mobile__link" href="/api">API</a>
      <a class="ce-mobile__link" href="/marketplace">Marketplace</a>
      <a class="ce-btn ce-btn--primary ce-mobile__cta" href="/rarog">Rarog</a>
    </div>
  </div>
</header>

<main class="ce-container ce-main">
';

echo $__out;
return $__out;
