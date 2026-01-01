# Nginx / aaPanel rewrite (CajeerEngine)

Если страницы вида `/news`, `/docs`, `/api/v1/ping` возвращают **404**, значит nginx пытается искать физические файлы
в `/public` и **не прокидывает** запросы в фронт-контроллер.

Решение: в конфиге сайта (aaPanel → Website → Settings → Rewrite) нужно включить `try_files`:

```nginx
location / {
  try_files $uri $uri/ /index.php?$args;
}
```

Также убедись, что root указывает на:

```
/www/wwwroot/<site>/public
```

И что PHP обработчик включён (enable-php-xx.conf).
