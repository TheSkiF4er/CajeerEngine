<?php
namespace Marketplace\V2;

class Http
{
    public static function request(string $method, string $url, array $headers = [], ?string $body = null): array
    {
        // file:// support for local registries
        if (str_starts_with($url, 'file://')) {
            $path = substr($url, 7);
            if (!is_file($path)) return ['ok'=>false,'status'=>404,'body'=>'','error'=>'file_not_found'];
            return ['ok'=>true,'status'=>200,'body'=>(string)file_get_contents($path),'error'=>null];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false) return ['ok'=>false,'status'=>$code,'body'=>'','error'=>$err ?: 'curl_error'];
        return ['ok'=>$code>=200 && $code<300,'status'=>$code,'body'=>(string)$resp,'error'=>null];
    }
}
