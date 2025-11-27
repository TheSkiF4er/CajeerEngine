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
 File: index.php
============================================================
*/

session_start();

$HTTPUSERAGENT = $_SERVER['HTTP_USER_AGENT'];
$REMOTEADDR = $_SERVER['REMOTE_ADDR'];

define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR.'/engine');
define('UPLOAD_DIR', ROOT_DIR.'/uploads');

include_once ENGINE_DIR.'/data/config.php';
include_once ENGINE_DIR.'/data/dbconfig.php';

$CONNECT = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if ($_COOKIE['settings'] != 'none') {
    setcookie('settings', 'none', strtotime('+30 days'), '/');
    setcookie('template', template, strtotime('+30 days'), '/');
    setcookie('language', language, strtotime('+30 days'), '/');
}

define('LANGUAGE_DIR', ROOT_DIR.'/languages/'.$_COOKIE['language']);
define('TEMPLATE_DIR', ROOT_DIR.'/templates/'.$_COOKIE['template']);

include_once LANGUAGE_DIR.'/system.php';
include_once LANGUAGE_DIR.'/website.php';
include_once ENGINE_DIR.'/engine.php';
?>
