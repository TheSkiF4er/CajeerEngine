<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{title}</title>
  <link rel="stylesheet" href="/assets/rarog/rarog.min.css">
</head>
<body>
<header class="rg-container rg-mt-3">
  <h1 class="rg-title">{title}</h1>

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
