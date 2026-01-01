<?php
namespace Content;
class TypeRepository {
  public function all(): array { return is_file(ROOT_PATH.'/system/content_types.php')?(array)require ROOT_PATH.'/system/content_types.php':[]; }
  public function get(string $k): ?array { $a=$this->all(); return $a[$k]??null; }
}
