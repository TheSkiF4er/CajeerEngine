<?php
declare(strict_types=1);

namespace Support;

/**
 * Minimal mailer with:
 * - mail() fallback (requires local MTA)
 * - optional SMTP via env vars
 *
 * Env vars:
 *   MAIL_TRANSPORT=mail|smtp
 *   MAIL_FROM=Support <support@cajeer.ru>
 *   MAIL_FROM_EMAIL=support@cajeer.ru
 *   MAIL_FROM_NAME=Support
 *
 * SMTP:
 *   MAIL_HOST, MAIL_PORT (25/587/465)
 *   MAIL_USER, MAIL_PASS
 *   MAIL_ENCRYPTION=none|tls|ssl  (tls uses STARTTLS)
 */
final class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        $transport = strtolower((string)getenv('MAIL_TRANSPORT'));
        if ($transport === 'smtp' && getenv('MAIL_HOST')) {
            return self::sendSmtp($to, $subject, $htmlBody, $textBody);
        }
        return self::sendMail($to, $subject, $htmlBody, $textBody);
    }

    private static function fromEmail(): string
    {
        $email = (string)getenv('MAIL_FROM_EMAIL');
        if ($email !== '') return $email;

        $from = (string)getenv('MAIL_FROM');
        if (preg_match('/<([^>]+)>/', $from, $m)) return trim($m[1]);

        return 'no-reply@' . (($_SERVER['HTTP_HOST'] ?? 'localhost') ?: 'localhost');
    }

    private static function fromName(): string
    {
        $name = (string)getenv('MAIL_FROM_NAME');
        if ($name !== '') return $name;

        $from = (string)getenv('MAIL_FROM');
        if ($from !== '' && !str_contains($from, '<')) return trim($from);

        return 'CajeerEngine';
    }

    private static function sendMail(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        $fromEmail = self::fromEmail();
        $fromName  = self::fromName();

        $boundary = 'ce_' . bin2hex(random_bytes(12));
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . self::encodeHeader($fromName) . ' <' . $fromEmail . '>';
        $headers[] = 'Reply-To: ' . $fromEmail;
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        if ($textBody === '') {
            $textBody = strip_tags($htmlBody);
        }

        $body  = '--' . $boundary . "\r\n";
        $body .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
        $body .= 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n";
        $body .= $textBody . "\r\n\r\n";
        $body .= '--' . $boundary . "\r\n";
        $body .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        $body .= 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";
        $body .= '--' . $boundary . '--';

        return @mail($to, self::encodeHeader($subject), $body, implode("\r\n", $headers));
    }

    private static function sendSmtp(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        $host = (string)getenv('MAIL_HOST');
        $port = (int)((string)getenv('MAIL_PORT') ?: '587');
        $user = (string)getenv('MAIL_USER');
        $pass = (string)getenv('MAIL_PASS');
        $enc  = strtolower((string)getenv('MAIL_ENCRYPTION'));
        if ($enc === '') $enc = 'tls';

        $fromEmail = self::fromEmail();
        $fromName  = self::fromName();
        $heloHost  = ($_SERVER['HTTP_HOST'] ?? 'localhost') ?: 'localhost';

        $remote = $host;
        $crypto = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if ($enc === 'ssl' || $port === 465) {
            $remote = 'ssl://' . $host;
        }

        $fp = @fsockopen($remote, $port, $errno, $errstr, 12);
        if (!$fp) return false;

        $read = function() use ($fp): string {
            $data = '';
            while (!feof($fp)) {
                $line = fgets($fp, 515);
                if ($line === false) break;
                $data .= $line;
                if (preg_match('/^\d{3} /', $line)) break;
            }
            return $data;
        };
        $send = function(string $cmd) use ($fp): void {
            fwrite($fp, $cmd . "\r\n");
        };
        $expect = function(string $resp, array $codes): bool {
            foreach ($codes as $c) {
                if (str_starts_with($resp, (string)$c)) return true;
            }
            return false;
        };

        $banner = $read();
        if (!$expect($banner, [220])) { fclose($fp); return false; }

        $send('EHLO ' . $heloHost);
        $ehlo = $read();
        if (!$expect($ehlo, [250])) {
            $send('HELO ' . $heloHost);
            $helo = $read();
            if (!$expect($helo, [250])) { fclose($fp); return false; }
        }

        if ($enc === 'tls') {
            $send('STARTTLS');
            $st = $read();
            if ($expect($st, [220])) {
                if (!stream_socket_enable_crypto($fp, true, $crypto)) { fclose($fp); return false; }
                $send('EHLO ' . $heloHost);
                $ehlo2 = $read();
                if (!$expect($ehlo2, [250])) { fclose($fp); return false; }
            }
        }

        if ($user !== '') {
            $send('AUTH LOGIN');
            $r = $read();
            if (!$expect($r, [334])) { fclose($fp); return false; }
            $send(base64_encode($user));
            $r = $read();
            if (!$expect($r, [334])) { fclose($fp); return false; }
            $send(base64_encode($pass));
            $r = $read();
            if (!$expect($r, [235])) { fclose($fp); return false; }
        }

        $send('MAIL FROM:<' . $fromEmail . '>');
        $r = $read();
        if (!$expect($r, [250])) { fclose($fp); return false; }

        $send('RCPT TO:<' . $to . '>');
        $r = $read();
        if (!$expect($r, [250, 251])) { fclose($fp); return false; }

        $send('DATA');
        $r = $read();
        if (!$expect($r, [354])) { fclose($fp); return false; }

        $boundary = 'ce_' . bin2hex(random_bytes(12));
        if ($textBody === '') $textBody = strip_tags($htmlBody);

        $headers = [];
        $headers[] = 'From: ' . self::encodeHeader($fromName) . ' <' . $fromEmail . '>';
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . self::encodeHeader($subject);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        $data  = implode("\r\n", $headers) . "\r\n\r\n";
        $data .= '--' . $boundary . "\r\n";
        $data .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n\r\n";
        $data .= $textBody . "\r\n\r\n";
        $data .= '--' . $boundary . "\r\n";
        $data .= 'Content-Type: text/html; charset=UTF-8' . "\r\n\r\n";
        $data .= $htmlBody . "\r\n\r\n";
        $data .= '--' . $boundary . '--' . "\r\n";
        $data .= '.'; // end DATA

        fwrite($fp, str_replace("\n.", "\n..", $data) . "\r\n");
        $r = $read();
        $send('QUIT');
        fclose($fp);

        return $expect($r, [250]);
    }

    private static function encodeHeader(string $s): string
    {
        // RFC 2047 encoded-word (UTF-8)
        if ($s === '') return '';
        return '=?UTF-8?B?' . base64_encode($s) . '?=';
    }
}
