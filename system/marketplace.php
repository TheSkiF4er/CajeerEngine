<?php
return [
    'base_url' => 'https://marketplace.cajeer.ru/api/v1',
    'require_signature' => true,
    'allow_local_upload' => true,
    'types' => ['plugin', 'theme', 'ui_block', 'content_type'],
    'trusted_publishers' => [
        // 'cajeer-official' => 'BASE64_ED25519_PUBKEY',
    ],
];
