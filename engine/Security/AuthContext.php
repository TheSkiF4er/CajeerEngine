<?php
namespace Security;

/**
 * Per-request auth context for Zero Trust.
 * Contains user identity, scopes, device posture and request metadata.
 */
class AuthContext
{
    public function __construct(
        public int $tenantId,
        public ?int $userId,
        public array $scopes = [],
        public ?string $deviceId = null,
        public int $deviceTrust = 0,
        public array $claims = []
    ) {}

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }
}
