<?php
namespace CajeerSDK;

class Client
{
    public function __construct(protected string $baseUrl, protected string $token) {}

    protected function request(string $method, string $path, array $query = [], ?array $json = null): array
    {
        $url = rtrim($this->baseUrl,'/') . $path;
        if ($query) $url .= '?' . http_build_query($query);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_filter([
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token,
            $json ? 'Content-Type: application/json' : null,
        ]));
        if ($json) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));

        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($body === false) return ['ok'=>false,'error'=>$err ?: 'curl_error'];
        $data = json_decode((string)$body, true);
        return ['ok'=>$code>=200 && $code<300, 'status'=>$code, 'data'=>$data, 'raw'=>$body];
    }

    public function contentV1(): ContentApi { return new ContentApi($this); }
    public function adminV1(): AdminApi { return new AdminApi($this); }
    public function _request(string $m, string $p, array $q=[], ?array $j=null): array { return $this->request($m,$p,$q,$j); }
}

class ContentApi {
    public function __construct(protected Client $c) {}
    public function listNews(array $query=[]): array { return $this->c->_request('GET','/api/v1/content/news', $query); }
}
class AdminApi {
    public function __construct(protected Client $c) {}
    public function me(): array { return $this->c->_request('GET','/api/v1/admin/me'); }
}
