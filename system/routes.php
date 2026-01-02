<?php
return [
  '/' => ['home', 'index'],
  '/news' => ['news', 'index'],
  '/docs' => ['docs', 'index'],
  '/api' => ['site', 'api'],
  '/marketplace' => ['marketplace', 'index'],
  '/marketplace/themes' => ['marketplace', 'themes'],
  '/marketplace/plugins' => ['marketplace', 'plugins'],
  '/marketplace/profile' => ['marketplace', 'profile'],
  '/login' => ['auth_local', 'login'],
  '/register' => ['auth_local', 'register'],

  '/rarog' => ['site', 'rarog'],
  '/arog' => ['arog', 'index'],
  '/news/view' => ['news', 'view'],

  // API v1
  '/api/v1/ping' => ['api', 'ping'],
  '/api/v1/content' => ['api', 'contentIndex'],
  '/api/v1/content/get' => ['api', 'contentGet'],
  '/api/v1/content/create' => ['api', 'contentCreate'],
  '/api/v1/content/update' => ['api', 'contentUpdate'],
  '/api/v1/content/delete' => ['api', 'contentDelete'],
  '/api/v1/content/publish' => ['api', 'contentPublish'],
  '/api/v1/health/live' => ['api', 'healthLive'],
  '/api/v1/health/ready' => ['api', 'healthReady'],
  '/metrics' => ['api', 'metrics'],

  // Admin
  '/admin' => ['admin', 'index'],
  '/admin/themes' => ['admin', 'themesIndex'],
  '/admin/themes/switch' => ['admin', 'themesSwitch'],
  '/admin/marketplace/status' => ['admin', 'marketplaceStatus'],
  '/admin/ui-builder' => ['admin', 'uiBuilderIndex'],
  '/admin/ui-builder/save' => ['admin', 'uiBuilderSave'],
  '/admin/marketplace/themes' => ['admin', 'marketplaceThemes'],
  '/admin/marketplace/plugins' => ['admin', 'marketplacePlugins'],

  '/page' => ['pages', 'view'],
  '/category' => ['category', 'view'],
  '/sitemap.xml' => ['seo', 'sitemap'],
  '/robots.txt' => ['seo', 'robots'],
];
