<?php
namespace Modules\MFA;

use Database\DB;
use MFA\TOTP;

class Controller
{
    protected function cfg(): array
    {
        return is_file(ROOT_PATH . '/system/identity.php') ? require ROOT_PATH . '/system/identity.php' : [];
    }

    public function totpEnroll()
    {
        $cfg = $this->cfg();
        $issuer = (string)($cfg['mfa']['totp_issuer'] ?? 'CajeerEngine');
        $digits = (int)($cfg['mfa']['totp_digits'] ?? 6);
        $period = (int)($cfg['mfa']['totp_period'] ?? 30);

        $userId = (int)($_SERVER['HTTP_X_CE_USER_ID'] ?? 0);
        if ($userId <= 0) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); return; }

        $secret = TOTP::generateSecret();
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $pdo = DB::pdo();
        if ($pdo) {
            $sql = ROOT_PATH . '/system/sql/identity_v3_3.sql';
            if (is_file($sql)) $pdo->exec(file_get_contents($sql));
            $pdo->prepare("INSERT INTO ce_mfa_factors(tenant_id,user_id,type,secret_base64,label,enabled,created_at,updated_at)
                           VALUES(:t,:u,'totp',:s,'Authenticator',1,NOW(),NOW())")
                ->execute([':t'=>$tenantId,':u'=>$userId,':s'=>$secret]);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'ok'=>true,
            'secret_base64'=>$secret,
            'otpauth_uri'=>"otpauth://totp/".rawurlencode($issuer).":".$userId."?secret=".$secret."&issuer=".rawurlencode($issuer)."&digits=".$digits."&period=".$period
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function totpVerify()
    {
        $cfg = $this->cfg();
        $digits = (int)($cfg['mfa']['totp_digits'] ?? 6);
        $period = (int)($cfg['mfa']['totp_period'] ?? 30);

        $userId = (int)($_SERVER['HTTP_X_CE_USER_ID'] ?? 0);
        if ($userId <= 0) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'unauthorized']); return; }

        $code = (string)($_POST['code'] ?? ($_GET['code'] ?? ''));
        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);

        $pdo = DB::pdo();
        if (!$pdo) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'db_required']); return; }

        $row = $pdo->query("SELECT * FROM ce_mfa_factors WHERE tenant_id=".(int)$tenantId." AND user_id=".(int)$userId." AND type='totp' AND enabled=1 ORDER BY id DESC LIMIT 1")
                   ->fetch(\PDO::FETCH_ASSOC);
        if (!$row) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'factor_not_found']); return; }

        $ok = TOTP::verify((string)$row['secret_base64'], $code, 1, $period, $digits);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>$ok], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
