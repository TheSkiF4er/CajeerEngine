<?php
namespace Content;
use Database\DB; use Core\Collector;
class ContentRepository {
  public function list(array $q): array {
    $pdo=DB::pdo(); $w=[]; $a=[];
    if(!empty($q['type'])){ $w[]="type=:type"; $a[':type']=$q['type']; }
    if(!empty($q['status'])){ $w[]="status=:status"; $a[':status']=$q['status']; }
    if(!empty($q['category_id'])){ $w[]="category_id=:cid"; $a[':cid']=(int)$q['category_id']; }
    if(!empty($q['slug'])){ $w[]="slug=:slug"; $a[':slug']=$q['slug']; }
    if(!empty($q['field']) && isset($q['value'])){
      $f=preg_replace('/[^a-zA-Z0-9_\.\-]/','',(string)$q['field']);
      $w[]="JSON_EXTRACT(fields, '$."{$f}"') = :fval"; $a[':fval']=(string)$q['value'];
    }
    $sort=in_array(($q['sort']??''),['id','created_at','updated_at','published_at'],true)?$q['sort']:'id';
    $order=strtolower((string)($q['order']??'desc'))==='asc'?'ASC':'DESC';
    $page=max(1,(int)($q['page']??1)); $per=min(100,max(1,(int)($q['per_page']??20))); $off=($page-1)*$per;
    $sql="SELECT * FROM ce_content_items".($w?(" WHERE ".implode(" AND ",$w)):"")." ORDER BY {$sort} {$order} LIMIT {$per} OFFSET {$off}";
    Collector::push('sql',$sql);
    $st=$pdo->prepare($sql); $st->execute($a); $rows=$st->fetchAll(\PDO::FETCH_ASSOC);
    return ['page'=>$page,'per_page'=>$per,'items'=>$rows];
  }
  public function get(int $id): ?array { $st=DB::pdo()->prepare("SELECT * FROM ce_content_items WHERE id=:id"); $st->execute([':id'=>$id]); $r=$st->fetch(\PDO::FETCH_ASSOC); return $r?:null; }
  public function create(array $d): array {
    $pdo=DB::pdo(); $now=date('Y-m-d H:i:s');
    $fields=(array)($d['fields']??[]); $rels=(array)($d['relationships']??[]);
    $item=['type'=>(string)($d['type']??'page'),'status'=>(string)($d['status']??'draft'),'title'=>(string)($d['title']??''),'slug'=>(string)($d['slug']??''),'category_id'=>(int)($d['category_id']??0),
      'fields'=>json_encode($fields,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),'relationships'=>json_encode($rels,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
      'created_at'=>$now,'updated_at'=>$now,'published_at'=>null];
    $pdo->prepare("INSERT INTO ce_content_items(type,status,title,slug,category_id,fields,relationships,created_at,updated_at,published_at)
      VALUES(:type,:status,:title,:slug,:category_id,:fields,:relationships,:created_at,:updated_at,:published_at)")->execute($item);
    $id=(int)$pdo->lastInsertId();
    $this->createVersion($id,$item['status'],(string)($d['body']??''),$item['fields'],$item['relationships'],$now);
    return $this->get($id);
  }
  public function update(int $id,array $d): ?array {
    $pdo=DB::pdo(); $cur=$this->get($id); if(!$cur) return null; $now=date('Y-m-d H:i:s');
    $cf=json_decode($cur['fields']??'[]',true)?:[]; $cr=json_decode($cur['relationships']??'[]',true)?:[];
    $fields=array_merge($cf,(array)($d['fields']??[])); $rels=array_merge($cr,(array)($d['relationships']??[]));
    $status=(string)($d['status']??$cur['status']); $slug=(string)($d['slug']??$cur['slug']); $title=(string)($d['title']??$cur['title']); $body=(string)($d['body']??'');
    $pdo->prepare("UPDATE ce_content_items SET status=:status,title=:title,slug=:slug,fields=:fields,relationships=:relationships,updated_at=:u WHERE id=:id")->execute([
      ':status'=>$status,':title'=>$title,':slug'=>$slug,
      ':fields'=>json_encode($fields,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
      ':relationships'=>json_encode($rels,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
      ':u'=>$now,':id'=>$id
    ]);
    $this->createVersion($id,$status,$body,json_encode($fields,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),json_encode($rels,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),$now);
    return $this->get($id);
  }
  public function delete(int $id): bool { DB::pdo()->prepare("DELETE FROM ce_content_versions WHERE item_id=:id")->execute([':id'=>$id]); return (bool)DB::pdo()->prepare("DELETE FROM ce_content_items WHERE id=:id")->execute([':id'=>$id]); }
  public function publish(int $id): ?array {
    $pdo=DB::pdo(); $cur=$this->get($id); if(!$cur) return null; $now=date('Y-m-d H:i:s');
    $pdo->prepare("UPDATE ce_content_items SET status='published', published_at=:p, updated_at=:u WHERE id=:id")->execute([':p'=>$now,':u'=>$now,':id'=>$id]);
    $this->createVersion($id,'published',(string)($cur['body']??''),(string)($cur['fields']??'{}'),(string)($cur['relationships']??'[]'),$now);
    return $this->get($id);
  }
  protected function createVersion(int $itemId,string $status,string $body,string $fieldsJson,string $relsJson,string $ts): void {
    DB::pdo()->prepare("INSERT INTO ce_content_versions(item_id,status,body,fields,relationships,created_at)
      VALUES(:item_id,:status,:body,:fields,:relationships,:created_at)")->execute([
        ':item_id'=>$itemId,':status'=>$status,':body'=>$body,':fields'=>$fieldsJson,':relationships'=>$relsJson,':created_at'=>$ts
      ]);
  }
}
