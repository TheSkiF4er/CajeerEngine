<?php
namespace Security;

use Observability\Logger;

/**
 * Policy-as-code (foundation).
 * Evaluates simple JSON rules loaded from system/policy.php.
 */
class PolicyEngine
{
    public function __construct(protected array $cfg = []) {}

    public function decide(AuthContext $ctx, array $req): array
    {
        $rules = (array)($this->cfg['rules'] ?? []);
        $path = (string)($req['path'] ?? '');
        foreach ($rules as $rule) {
            $when = (array)($rule['when'] ?? []);
            $prefix = (string)($when['path_prefix'] ?? '');
            if ($prefix !== '' && !str_starts_with($path, $prefix)) continue;

            // allow_if.scopes_any
            $allowIf = (array)($rule['allow_if'] ?? []);
            $needAny = (array)($allowIf['scopes_any'] ?? []);
            if ($needAny) {
                foreach ($needAny as $s) {
                    if ($ctx->hasScope((string)$s)) {
                        return ['allow'=>true,'rule'=>$rule['id'] ?? '','reason'=>'scope_match'];
                    }
                }
                return ['allow'=>false,'rule'=>$rule['id'] ?? '','reason'=>'missing_scope'];
            }
        }
        // default allow (RBAC layer can additionally deny)
        return ['allow'=>true,'rule'=>'default','reason'=>'no_policy_match'];
    }
}
