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

  '/api/ai/policy' => ['AI', 'policy'],
  '/api/ai/optin' => ['AI', 'optIn'],
  '/api/ai/requests' => ['AI', 'requests'],
  '/api/ai/request' => ['AI', 'request'],
  '/api/ai/recommendations' => ['AI', 'recommendations'],
  '/api/ai/recommend/run' => ['AI', 'recommendRun'],
  '/api/ai/alerts/run' => ['AI', 'alertsRun'],

  '/api/edge/route' => ['Edge', 'route'],
  '/api/edge/config' => ['Edge', 'config'],
  '/api/edge/canary' => ['Edge', 'canary'],

  '/api/cp/status' => ['ControlPlane', 'status'],
  '/api/cp/fleet' => ['ControlPlane', 'fleet'],
  '/api/cp/policies/get' => ['ControlPlane', 'policyGet'],
  '/api/cp/policies/set' => ['ControlPlane', 'policySet'],
  '/api/cp/insights/tenants' => ['ControlPlane', 'insightsTenants'],
  '/api/cp/health/compute' => ['ControlPlane', 'healthCompute'],
  '/api/cp/capacity/forecast' => ['ControlPlane', 'capacityForecast'],
  '/api/cp/rollouts/plan' => ['ControlPlane', 'rolloutPlan'],
  '/api/cp/rollouts/create' => ['ControlPlane', 'rolloutCreate'],
  '/api/cp/rollouts/list' => ['ControlPlane', 'rolloutList'],
  '/api/cp/rollouts/step' => ['ControlPlane', 'rolloutStep'],
  '/api/cp/heal/enqueue' => ['ControlPlane', 'healEnqueue'],
  '/api/cp/heal/run' => ['ControlPlane', 'healRun'],
];
