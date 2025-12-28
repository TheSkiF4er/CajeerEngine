<?php
namespace UIBuilder;
use Database\DB;
class Repository
{
    public function get(int $contentId): ?array
    {
        $pdo = DB::pdo();
        $st = $pdo->prepare("SELECT layout_json FROM ce_ui_layouts WHERE content_id=:id LIMIT 1");
        $st->execute([':id'=>$contentId]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if(!$row) return null;
        $arr = json_decode((string)$row['layout_json'], true);
        return is_array($arr) ? $arr : null;
    }
    public function save(int $contentId, array $layout): void
    {
        $pdo = DB::pdo();
        $json = json_encode($layout, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $pdo->prepare("INSERT INTO ce_ui_layouts(content_id, layout_json, updated_at)
          VALUES(:id,:json,NOW())
          ON DUPLICATE KEY UPDATE layout_json=:json2, updated_at=NOW()")
          ->execute([':id'=>$contentId,':json'=>$json,':json2'=>$json]);
    }
    public function delete(int $contentId): void
    {
        DB::pdo()->prepare("DELETE FROM ce_ui_layouts WHERE content_id=:id")->execute([':id'=>$contentId]);
    }
}
