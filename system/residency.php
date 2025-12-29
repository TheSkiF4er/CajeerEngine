<?php
return [
  // Data residency: map tenant/site to allowed storage regions
  'enabled' => false,
  'default_region' => 'eu',
  'regions' => [
    'eu' => ['name' => 'EU', 'allowed' => true],
    'us' => ['name' => 'US', 'allowed' => true],
  ],
];
