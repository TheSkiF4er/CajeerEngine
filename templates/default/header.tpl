<!doctype html>
<html lang="ru">
<head>
{meta_tags}
{jsonld}

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{title}</title>
  <link rel="stylesheet" href="/assets/rarog/rarog.min.css?v={app_version}">
  <link rel="stylesheet" href="/assets/themes/default/theme.css?v={app_version}">
</head>
<body>
<header class="rg-container rg-mt-3">
  <h1 class="rg-title">{title}</h1>

  <nav class="rg-mt-2">
    <div class="rg-btn-group">
      <a class="rg-btn rg-btn-secondary" href="/">Главная</a>
      <a class="rg-btn rg-btn-secondary" href="/news">Обновления</a>
      <a class="rg-btn rg-btn-secondary" href="/docs">Docs</a>
      <a class="rg-btn rg-btn-secondary" href="/arog">Arog</a>
    </div>
  </nav>


  [if logged]
    <div class="rg-alert rg-alert-success">Вы вошли как: {user.name}</div>
  [else]
    <div class="rg-alert rg-alert-warning">Гость: {user.name}</div>
  [/if]

  [group=1]
    <div class="rg-alert rg-alert-info">Группа: Администратор</div>
  [/group]

  [not-group=1]
    <div class="rg-alert rg-alert-secondary">Нет прав администратора</div>
  [/not-group]
</header>
