<?php
// Perf regression harness (foundation).
$start = microtime(true);
$iterations = 1000;
$uri = '/';
for ($i=0; $i<$iterations; $i++) {
    $u = parse_url('http://localhost'.$uri, PHP_URL_PATH);
    if ($u !== '/') { echo "bad"; exit(1); }
}
$elapsed = (microtime(true) - $start) * 1000;
echo json_encode([
  'ok'=>true,
  'bench'=>'router_parse_url',
  'iterations'=>$iterations,
  'ms_total'=>round($elapsed, 2),
  'ms_per_iter'=>round($elapsed/$iterations, 6),
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).PHP_EOL;
