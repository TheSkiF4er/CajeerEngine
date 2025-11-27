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
 File: antibot.php
------------------------------------------------------------
 Use: CAPTCHA
============================================================
*/

session_start();
$Random = rand(100000, 999999);
$_SESSION['antibot'] = md5($Random);
$im = imagecreatetruecolor(110, 30);
imagefilledrectangle($im, 0, 0, 110, 30, imagecolorallocate($im, 0, 0, 0));
imagettftext($im, 40, 0, 15, 23, imagecolorallocate($im, 255, 215, 0), '/font/antibot.ttf', $Random);
header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-type: image/gif');
imagegif($im);
imagedestroy($im);
?>
