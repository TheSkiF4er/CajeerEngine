<?php
return [
  // active theme slug
  'active' => 'default',
  // themes root
  'themes_path' => ROOT_PATH . '/templates/themes',
  // public assets base url segment
  'assets_base' => '/assets/themes',
  // allow switching at runtime (admin/cli)
  'allow_switch' => true,
  // list (optional) - if empty, ThemeManager will scan directory
  'themes' => [
    'default' => ['title' => 'Default'],
    'rarog-official' => ['title' => 'Rarog Official (Cajeer)'],
  ],
];
