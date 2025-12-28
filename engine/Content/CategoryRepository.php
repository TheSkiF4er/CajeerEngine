<?php
namespace Content;
use Database\DB;
class CategoryRepository {
  public function list(): array { return DB::pdo()->query("SELECT * FROM ce_categories ORDER BY parent_id ASC, sort_order ASC, id ASC")->fetchAll(\PDO::FETCH_ASSOC); }
  public function get(int $id): ?array { $st=DB::pdo()->prepare("SELECT * FROM ce_categories WHERE id=:id"); $st->execute([':id'=>$id]); $r=$st->fetch(\PDO::FETCH_ASSOC); return $r?:null; }
}
