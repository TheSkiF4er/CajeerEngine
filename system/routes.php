<?php
return [
  '/' => ['news', 'index'],
  '/news' => ['news', 'index'],
  '/news/view' => ['news', 'view'],
  '/admin' => ['admin', 'index'],
,
  '/page' => ['pages', 'view'],
  '/category' => ['category', 'view'],
,
    '/sitemap.xml' => ['seo', 'sitemap'],
    '/robots.txt' => ['seo', 'robots']
];
