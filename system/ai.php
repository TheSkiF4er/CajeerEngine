<?php
return [
  'governance' => [
    'default_opt_in' => false,
    'prompt_transparency' => true,
    'store_requests' => true,
    'redaction' => [
      'enabled' => true,
      'rules' => [
        ['type'=>'regex','pattern'=>'/([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})/i','replace'=>'[redacted_email]'],
        ['type'=>'regex','pattern'=>'/(Bearer\s+)[A-Za-z0-9\-\._~\+\/]+=*/i','replace'=>'$1[redacted_token]'],
        ['type'=>'regex','pattern'=>'/\b\d{12,19}\b/','replace'=>'[redacted_number]'],
      ],
    ],
    'data_boundaries' => [
      'allow' => [
        'content' => true,
        'templates' => true,
        'logs' => false,
        'secrets' => false,
        'pii' => false,
      ],
    ],
  ],

  'providers' => [
    'mock' => ['enabled' => true],
    // Enable 'http' provider to connect your own AI gateway:
    // 'http' => ['enabled'=>false,'endpoint'=>'https://your-ai-gateway/v1/chat','token'=>'','timeout_ms'=>8000],
  ],

  'defaults' => [
    'provider' => 'mock',
    'model' => 'default',
  ],
];
