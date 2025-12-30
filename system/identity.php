<?php
return [
  // OIDC providers configuration (production-ready wiring, tokens validation is stub unless configured)
  'oidc' => [
    // Example:
    // 'corp' => [
    //   'issuer' => 'https://login.example.com',
    //   'client_id' => '',
    //   'client_secret' => '',
    //   'redirect_uri' => 'https://your-site.com/api/auth/oidc/callback',
    //   'scopes' => ['openid','profile','email'],
    // ],
  ],

  // SAML providers configuration (production wiring, XML validation stub unless configured)
  'saml' => [
    // 'corp' => [
    //   'idp_metadata_url' => '',
    //   'sp_entity_id' => '',
    //   'acs_url' => 'https://your-site.com/api/auth/saml/acs',
    // ],
  ],

  'mfa' => [
    'totp_issuer' => 'CajeerEngine',
    'totp_digits' => 6,
    'totp_period' => 30,
    'require_mfa_for_admin' => true,
  ],
];
