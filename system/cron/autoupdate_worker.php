<?php
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/engine/bootstrap.php';

$cfg = require ROOT_PATH . '/system/config.php';
\Database\DB::connect($cfg['db']);

\AutoUpdate\Worker::runOnce();
