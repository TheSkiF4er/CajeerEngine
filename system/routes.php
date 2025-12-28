<?php
return [
  '/' => ['news', 'index'],
  '/news' => ['news', 'index'],
  '/news/view' => ['news', 'view'],
  '/admin' => ['admin', 'index'],
    '/admin/themes' => ['admin', 'themesIndex'],
    '/admin/themes/switch' => ['admin', 'themesSwitch'],
    '/admin/marketplace/status' => ['admin', 'marketplaceStatus'],
,
  '/page' => ['pages', 'view'],
  '/category' => ['category', 'view'],
,
    '/sitemap.xml' => ['seo', 'sitemap'],
    '/robots.txt' => ['seo', 'robots']
];
