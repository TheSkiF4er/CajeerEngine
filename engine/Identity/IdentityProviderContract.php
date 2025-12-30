<?php
namespace Identity;

interface IdentityProviderContract
{
    public function type(): string; // oidc|saml
    public function name(): string;
    public function startAuth(array $params = []): array; // returns redirect URL, state, etc.
    public function handleCallback(array $params = []): array; // returns subject + claims
}
