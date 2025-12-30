<?php
return [
  // Enforce per-request auth context and continuous authorization.
  'enabled' => true,

  // Optional device posture (foundation): require a device record and minimum trust level.
  'device_posture' => [
    'enabled' => false,
    'min_trust' => 50, // 0..100
  ],

  // Continuous auth: re-check policies every request, not only at login.
  'continuous_authorization' => true,

  // Access log settings
  'access_logs' => [
    'enabled' => true,
    'immutable_chain' => true,
  ],
];
