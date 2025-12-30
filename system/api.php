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

  '/api/frontend/isr/purge' => ['Frontend', 'purgeIsr'],
  '/api/builder/lock' => ['Builder', 'lock'],
  '/api/builder/patch' => ['Builder', 'patch'],
  '/api/ab/assign' => ['AB', 'assign'],
  '/api/nocode/workflow/run' => ['NoCode', 'runWorkflow'],
  '/api/nocode/form/submit' => ['NoCode', 'submitForm'],

  '/api/intelligence/usage' => ['Intelligence', 'usage'],
  '/api/intelligence/perf' => ['Intelligence', 'perf'],
  '/api/intelligence/cost' => ['Intelligence', 'cost'],
  '/api/automation/run' => ['Automation', 'runOnce'],
  '/api/ai/suggest/content' => ['AI', 'suggestContent'],
  '/api/ai/suggest/layout' => ['AI', 'suggestLayout'],
  '/api/ai/admin/copilot' => ['AI', 'adminCopilot'],
];
