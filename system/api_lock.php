<?php
/**
 * API Freeze for 3.x LTS
 * - Public API versions are locked.
 * - Any breaking changes require a new major (4.0).
 */
return [
  'policy' => [
    'major' => 3,
    'freeze' => true,
    'since' => '3.6.0',
    'deprecation_window_minor_releases' => 3,
  ],

  'public' => [
    'admin' => 'v1',
    'content' => 'v2',
    'marketplace' => 'v2',
    'auth' => 'v2',
    'observability' => 'v1',
    'automation' => 'v1',
  ],
];
