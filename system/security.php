<?php
return [
  'csrf' => [
    'enabled' => true,
    'token_ttl' => 7200,
    'cookie_name' => 'ce_csrf',
    'header_name' => 'X-CSRF-Token',
    'field_name' => '_csrf',
  ],
  'rate_limit' => [
    'enabled' => true,
    // per key (ip+route) window seconds
    'window' => 60,
    'max' => 120,
  ],
  'ip_filter' => [
    'enabled' => false,
    'allow' => [
      // '127.0.0.1',
      // '192.168.0.0/16',
    ],
    'deny' => [
      // '10.0.0.0/8',
    ],
  ],
  'xss' => [
    'enabled' => true,
  ],
  'audit' => [
    'enabled' => true,
    'store_ip' => true,
    'store_user_agent' => true,
  ],
];
