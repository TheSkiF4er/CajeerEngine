<?php
return [
    'base_url' => 'https://marketplace.cajeer.ru/api/v1',
    'require_signature' => true,
    'allow_local_upload' => true,
    'types' => ['plugin', 'theme', 'ui_block', 'content_type'],
    'trusted_publishers' => [
        // 'cajeer-official' => 'BASE64_ED25519_PUBKEY',
    ],
  'registries' => [
    'official' => [
      'name' => 'Cajeer Official',
      'base_url' => 'https://marketplace.cajeer.ru',
      'public_key' => ROOT_PATH . '/system/keys/official_pub.pem',
      'verification_level' => 'trusted',
      'enabled' => true,
    ],
  ],
  'index' => ['auto_sync' => false,'cache_ttl_sec' => 3600],
  'security' => ['require_signature' => true,'allow_unverified_publishers' => false,'sandbox_preflight' => true],
  'monetization' => ['enabled' => false,'provider' => 'external','webhook_secret' => ''],
];
