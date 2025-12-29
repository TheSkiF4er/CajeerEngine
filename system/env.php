<?php
return [
  'env' => getenv('APP_ENV') ?: 'dev',
  'api' => [
    'version' => '1',
    'require_header' => true,
    'header_name' => 'X-API-Version',
    'allow_query_fallback' => true,
  ],
  'observability' => [
    'enabled' => true,
  ],
];
