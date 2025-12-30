<?php
return [
  'enabled' => true,
  'tokens' => [
    // 'token_value' => ['name'=>'default','scopes'=>['read','write']]
  ],

  '/api/auth/oidc/start' => ['Auth', 'oidcStart'],
  '/api/auth/oidc/callback' => ['Auth', 'oidcCallback'],
  '/api/auth/saml/start' => ['Auth', 'samlStart'],
  '/api/auth/saml/acs' => ['Auth', 'samlAcs'],

  '/api/mfa/totp/enroll' => ['MFA', 'totpEnroll'],
  '/api/mfa/totp/verify' => ['MFA', 'totpVerify'],

  '/api/compliance/soc2' => ['Compliance', 'soc2'],
];
