# Rarog

Rarog — гибридный CSS‑фреймворк и дизайн‑система: **design‑tokens + utilities + компоненты + JS‑ядро**.
Задуман как практичная альтернатива связке **Tailwind CSS + Bootstrap** и подходит как для Cajeer‑экосистемы, так и для обычных стеков (Laravel, React, Vue, Next.js, SvelteKit и т.д.). 

## Что входит в Rarog 3.x

- **Tokens** (`rarog.tokens.json`) и theme‑packs
- Utility‑классы (responsive/state‑префиксы, JIT, произвольные значения)
- Компоненты + сетка
- JS‑ядро и UI‑киты (Admin/Landing/SaaS) 

## Быстрый старт

### CDN / статическое подключение

```html
<link rel="stylesheet" href="/css/rarog-core.min.css">
<link rel="stylesheet" href="/css/rarog-utilities.min.css">
<link rel="stylesheet" href="/css/rarog-components.min.css">
<link rel="stylesheet" href="/css/rarog-theme-default.min.css">
<script src="/js/rarog.umd.js" defer></script>
```


### npm + CLI + JIT

```bash
npm install rarog-css
npx rarog build
```


## Rarog в CajeerEngine

В CajeerEngine Rarog используется как UI‑слой: админка, системные страницы, базовая тема.
Подключение ассетов (пример):

```html
<link rel="stylesheet" href="/assets/rarog/rarog.min.css?v={app_version}">
<link rel="stylesheet" href="/assets/themes/default/theme.css?v={app_version}">
```

## Ссылки

- Репозиторий: https://github.com/TheSkiF4er/Rarog 
- Документация: `rarog.cajeer.ru` 
