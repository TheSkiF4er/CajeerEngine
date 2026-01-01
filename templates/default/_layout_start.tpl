<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{seo_title}</title>
  <meta name="description" content="{seo_description}">
  <link rel="canonical" href="{seo_canonical}">
  {seo_og}
  {seo_twitter}

  <!-- Rarog (optional, if installed) -->
  <link rel="stylesheet" href="/assets/rarog/rarog.min.css?v={app_version}">
  <!-- CajeerEngine theme -->
  <link rel="stylesheet" href="/assets/themes/default/theme.css?v={app_version}">
  {head_extra}
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
      <a class="ce-nav__link" href="/api/v1/ping">API</a>
      <a class="ce-btn ce-btn--primary" href="/admin">Админка</a>
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
      <a class="ce-mobile__link" href="/api/v1/ping">API</a>
      <a class="ce-btn ce-btn--primary ce-mobile__cta" href="/admin">Админка</a>
    </div>
  </div>
</header>

<main class="ce-container ce-main">
