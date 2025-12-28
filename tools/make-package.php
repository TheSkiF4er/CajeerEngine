<?php
/**
 * Simple package builder (developer utility).
 * Usage:
 *   php tools/make-package.php out.cajeerpkg id version channel files_dir
 */
if ($argc < 6) {
  echo "Usage: php tools/make-package.php out.cajeerpkg <id> <version> <channel> <files_dir>\n";
  exit(2);
}
$out = $argv[1];
$id = $argv[2];
$ver = $argv[3];
$ch = $argv[4];
$dir = rtrim($argv[5], '/');

$manifest = [
  'id'=>$id,
  'type'=>'pkg',
  'version'=>$ver,
  'channel'=>$ch,
  'title'=>$id,
  'created_at'=>date('c'),
];

$z = new ZipArchive();
if ($z->open($out, ZipArchive::CREATE|ZipArchive::OVERWRITE) !== true) {
  echo "Cannot create $out\n"; exit(1);
}
$z->addFromString('manifest.json', json_encode($manifest, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
  if (!$f->isFile()) continue;
  $rel = substr($f->getPathname(), strlen($dir)+1);
  $z->addFile($f->getPathname(), 'files/'.$rel);
}
$z->close();
echo "Built: $out\n";
