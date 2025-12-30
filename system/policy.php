<?php
return [
  // Policy-as-code (foundation): simple JSON rules; real OPA/Rego integration planned in 3.3.x
  // Rules are evaluated in Security\PolicyEngine.
  'rules' => [
    // Example:
    // [
    //   'id' => 'admin_api_requires_scope',
    //   'when' => ['path_prefix' => '/api/admin'],
    //   'allow_if' => ['scopes_any' => ['admin.read','admin.write']],
    // ],
  ],
];
