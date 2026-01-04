<?php
/**
 * Routes map: "path" => ["module", "method"]
 * Note: keep this file deterministic; do not include runtime logic.
 */
return [
  // Public
  '/' => ['home', 'index'],
  '/news' => ['news', 'index'],
  '/news/view' => ['news', 'view'],
  '/docs' => ['docs', 'index'],
  '/api' => ['site', 'api'],
  '/rarog' => ['rarog', 'index'],

  // Resources (public витрина)
  '/resources' => ['resources', 'index'],
  '/resources/themes' => ['resources', 'themes'],
  '/resources/plugins' => ['resources', 'plugins'],
  '/resources/profile' => ['resources', 'profile'],

  // Legacy Marketplace -> Resources (301)
  '/marketplace' => ['resources', 'redirectMarketplace'],
  '/marketplace/themes' => ['resources', 'redirectMarketplace'],
  '/marketplace/plugins' => ['resources', 'redirectMarketplace'],
  '/marketplace/profile' => ['resources', 'redirectMarketplace'],

  // Account (public users, not admin)
  '/login' => ['account', 'login'],
  '/register' => ['account', 'register'],
  '/logout' => ['account', 'logout'],
  '/profile' => ['account', 'profile'],
  '/verify' => ['account', 'verify'],
  '/resend' => ['account', 'resendVerification'],
  '/forgot' => ['account', 'forgot'],
  '/reset' => ['account', 'reset'],

  // Content pages
  '/page' => ['pages', 'view'],
  '/category' => ['category', 'view'],

  // Admin
  '/admin' => ['admin', 'index'],
  '/admin/themes' => ['admin', 'themesIndex'],
  '/admin/themes/switch' => ['admin', 'themesSwitch'],
  '/admin/ui-builder' => ['admin', 'uiBuilderIndex'],
  '/admin/ui-builder/save' => ['admin', 'uiBuilderSave'],
  '/admin/marketplace/status' => ['admin', 'marketplaceStatus'],
  '/admin/marketplace/themes' => ['admin', 'marketplaceThemes'],
  '/admin/marketplace/plugins' => ['admin', 'marketplacePlugins'],
];
