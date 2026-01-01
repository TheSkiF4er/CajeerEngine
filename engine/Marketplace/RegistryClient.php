<?php
namespace Marketplace;
use Observability\Logger;
class RegistryClient {
  public static function get(string $url): array {
    $ctx=stream_context_create(['http'=>['timeout'=>10]]);
    $raw=@file_get_contents($url,false,$ctx);
    if($raw===false) return ['ok'=>false,'error'=>'fetch_failed','url'=>$url];
    $data=json_decode($raw,true);
    if(!is_array($data)) return ['ok'=>false,'error'=>'invalid_json','url'=>$url];
    return $data;
  }
  public static function index(string $base): array {
    $u=rtrim($base,'/').'/api/v1/registry/index'; Logger::info('marketplace.registry.index',['url'=>$u]); return self::get($u);
  }
  public static function search(string $base,string $q): array {
    $u=rtrim($base,'/').'/api/v1/registry/search?q='.urlencode($q); Logger::info('marketplace.registry.search',['url'=>$u,'q'=>$q]); return self::get($u);
  }
  public static function package(string $base,string $key,string $version): array {
    $u=rtrim($base,'/').'/api/v1/registry/package?key='.urlencode($key).'&version='.urlencode($version);
    Logger::info('marketplace.registry.package',['url'=>$u,'key'=>$key,'version'=>$version]); return self::get($u);
  }
}
