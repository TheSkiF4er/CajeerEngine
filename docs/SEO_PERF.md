# SEO & Performance (v1.5)

## SEO API
Использование в контроллерах:
```php
$seo = \Core\KernelSingleton::container()->get('seo');
$seo->setTitle('Title');
$seo->setDescription('Description');
$seo->setCanonical('https://example.com/page');
$seo->addOg('og:type','article');
$seo->addTwitter('twitter:card','summary');
$seo->addJsonLd([...]);
```

В шаблонах в `<head>` должны быть переменные:
- `{meta_tags}`
- `{jsonld}`

## Sitemap / Robots
- `/sitemap.xml` — генерируется динамически на основе опубликованного контента (до 5000 записей).
- `/robots.txt` — отдаёт `public/robots.txt` (можно редактировать).

## Performance
### Page cache
- Включается `cache.enabled=true`.
- Кэшируется только GET и только фронтенд (не `/admin`), пропускается для залогиненных.
- TTL: `cache.page_ttl`.

### Fragment cache
```php
\Cache\Cache::remember('key', 300, fn() => expensive(), ['tag1','tag2']);
```

### Инвалидация
- По тегам: `Cache::invalidateTag('content')`
- Полная очистка: `php cli/cajeer cache:clear`

## Lazy-loading
Глобально добавляется `loading="lazy"` к `<img>` тегам через `Seo\Html::lazyImages()` на уровне Kernel.
