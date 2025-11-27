<?php
/*
============================================================
 Cajeer Engine - by Cajeer Team 
------------------------------------------------------------
 https://cajeer.com/
------------------------------------------------------------
 Copyright (c) 2013-2025 Cajeer Team 
============================================================
 This code is protected by copyright.
 All rights reserved.
 All trademarks are the property of their respective owners.
============================================================
 File: cron.php
-----------------------------------------------------
 Use: Performing automatic operations
============================================================
*/

define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR.'/engine');
include_once ENGINE_DIR.'/data/dbconfig.php';
$CONNECT = mysqli_connect(SERVER, USERNAME, PASSWORD, NAME);

mysqli_query($CONNECT, "DELETE FROM `chat` WHERE `time` < SUBTIME(NOW(), 30 0:0:0)");
mysqli_query($CONNECT, "DELETE FROM `online` WHERE `time` < SUBTIME(NOW(), 0 1:0:0)");
?>
