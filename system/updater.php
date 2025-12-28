<?php
return [
  // Can be local file path or https URL returning manifest JSON
  'manifest' => ROOT_PATH . '/system/updates/manifest.json',
  'channel' => 'stable', // stable|beta
  'backups_path' => ROOT_PATH . '/storage/backups',
  'updates_path' => ROOT_PATH . '/storage/updates',
];
