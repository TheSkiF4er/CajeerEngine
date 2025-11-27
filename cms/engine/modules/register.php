<?php
/*
============================================================
 Cajeer Engine - by Cajeer Team 
------------------------------------------------------------
 https://cajeer.com/
------------------------------------------------------------
 Copyright (c) 2013-2025 Cajeer Team 
============================================================
 All rights reserved.
 All trademarks are the property of their respective owners.
============================================================
 File: register.php
-----------------------------------------------------
 Use: registration of visitors
============================================================
*/

ULogin(0);
if ($Module == 'registration' and $_POST['enter']) {
	//check_antibot($_POST['antibot']);
	//if ($_SESSION['antibot'] != md5($_POST['antibot'])) MessageSend(1, LANGSYS16);
	sleep(5);
	check_name($_POST['name']);
	check_login($_POST['login']);
	check_password($_POST['password']);
	$_POST['password'] = GenPass($_POST['password'], $_POST['login']);
	if (!$_POST['login'] or !$_POST['password'] or !$_POST['name']/* or !$_POST['antibot']*/) MessageSend(1, LANGSYS15);
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `login` = '$_POST[login]'"));
	if ($Row['login']) MessageSend(1, LANGSYS27.' <b>'.$_POST['login'].'</b> '.LANGSYS28);
	$UID = rand(100000000, 999999999);
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `uid` FROM `users` WHERE `uid` = '$UID'"));
	if ($Row['uid']) MessageSend(1, LANGSYS26);
	mysqli_query($CONNECT, "INSERT INTO `users` VALUES ('', '$UID', '$_POST[login]', '', '', '$_POST[password]', 0, '$_POST[name]', '', 0, '', '', 0, '', '', '', 0, 0, '', '', 0, NOW(), '$REMOTEADDR', '$HTTPUSERAGENT', NOW(), '$REMOTEADDR', '$HTTPUSERAGENT')");
	MessageSend(2, LANGSYS25, '/account/');
}
?>
