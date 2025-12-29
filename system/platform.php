<?php
return [
  'enabled' => true,

  'enforced' => true,
  'require_tenant' => true,


  // Tenant resolution strategy:
  // - host: tenant by subdomain (tenant.example.com) or custom domain mapping
  // - header: X-Tenant-Id
  // - query: tenant_id
  'resolver' => [
    'mode' => 'host',
    'host' => [
      'base_domain' => 'example.com', // for subdomain mapping, optional
    ],
    'header_name' => 'X-Tenant-Id',
    'query_name' => 'tenant_id',
  ],

  // Site isolation (multi-site per tenant)
  // - host: site by host mapping
  // - header: X-Site-Id
  'site' => [
    'mode' => 'host',
    'header_name' => 'X-Site-Id',
  ],

  // Limits & billing hooks
  'limits' => [
    'enabled' => false,
    'default_plan' => 'free',
    'plans' => [
      'free' => ['sites'=>1, 'users'=>3, 'api_rpm'=>60, 'storage_mb'=>200],
      'pro'  => ['sites'=>10,'users'=>50,'api_rpm'=>600,'storage_mb'=>5000],
    ],
  ],

  // External billing integration hook endpoint (optional)
  'billing' => [
    'enabled' => false,
    'webhook_secret' => '',
  ],

  // Auto-updates / staged rollout
  'autoupdate' => [
    'enabled' => false,
    'channel' => 'stable', // stable|beta|canary
    'strategy' => 'staged', // staged|canary|all
    'batch_size' => 50,
    'healthcheck_url' => '/api/v1/platform/health',
  ],
];
