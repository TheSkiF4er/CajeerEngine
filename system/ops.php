<?php
return [
  'sla' => [
    'enabled' => false,
    'webhook_url' => '',
    'webhook_secret' => '',
  ],
  'incident' => [
    'enabled' => false,
    'webhook_url' => '',
    'webhook_secret' => '',
  ],
  'profiles' => [
    'dev' => ['strict' => false],
    'staging' => ['strict' => true],
    'prod' => ['strict' => true],
  ],
];
