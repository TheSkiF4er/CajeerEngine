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
 File: sitelogin.php
-----------------------------------------------------
 Use: authorization of visitors to the site
============================================================
*/

ULogin(0);
if ($Module == 'authorization' and $_POST['enter']) {
	//check_antibot($_POST['antibot']);
	//if ($_SESSION['antibot'] != md5($_POST['antibot'])) MessageSend(1, LANGSYS16);
	sleep(5);
	check_login($_POST['login']);
	check_password($_POST['password']);
	$_POST['password'] = GenPass($_POST['password'], $_POST['login']);
	if (!$_POST['login'] or !$_POST['password']/* or !$_POST['antibot']*/) MessageSend(1, LANGSYS15);
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `password` FROM `users` WHERE `login` = '$_POST[login]'"));
	if ($Row['password'] != $_POST['password']) MessageSend(1, LANGSYS29);
	mysqli_query($CONNECT, "UPDATE `users` SET `lastdate` = NOW() WHERE `login` = '$_POST[login]'");
	mysqli_query($CONNECT, "UPDATE `users` SET `ip` = '$REMOTEADDR' WHERE `login` = '$_POST[login]'");
	mysqli_query($CONNECT, "UPDATE `users` SET `browser` = '$HTTPUSERAGENT' WHERE `login` = '$_POST[login]'");
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `uid`, `email`, `pnumber`, `password`, `group`, `name`, `surname`, `gender`, `dob`, `ncity`, `city`, `bio`, `company`, `teams`, `languages`, `planguages`, `website`, `projects`, `country`, `lastdate`, `ip`, `browser`, `regdate`, `regip`, `regbrowser` FROM `users` WHERE `login` = '$_POST[login]'"));
	$_SESSION['USER_ID'] = $Row['id'];
	$_SESSION['USER_UID'] = $Row['uid'];
	$_SESSION['USER_LOGIN'] = $_POST['login'];
	$_SESSION['USER_EMAIL'] = $Row['email'];
	$_SESSION['USER_PNUM'] = $Row['pnumber'];
	$_SESSION['USER_PASSWORD'] = $Row['password'];
	$_SESSION['USER_GROUP'] = $Row['group'];
	$_SESSION['USER_NAME'] = $Row['name'];
	$_SESSION['USER_SURNAME'] = $Row['surname'];
	$_SESSION['USER_GEN'] = $Row['gender'];
	$_SESSION['USER_DOB'] = $Row['dob'];
	$_SESSION['USER_NCITY'] = $Row['ncity'];
	$_SESSION['USER_CITY'] = $Row['city'];
	$_SESSION['USER_BIO'] = $Row['bio'];
	$_SESSION['USER_COM'] = $Row['company'];
	$_SESSION['USER_TEAM'] = $Row['teams'];
	$_SESSION['USER_LANG'] = $Row['languages'];
	$_SESSION['USER_PLANG'] = $Row['planguages'];
	$_SESSION['USER_WSITE'] = $Row['website'];
	$_SESSION['USER_PROJECTS'] = $Row['projects'];
	$_SESSION['USER_COUNTRY'] = $Row['country'];
	$_SESSION['USER_LASTDATE'] = $Row['lastdate'];
	$_SESSION['USER_IP'] = $Row['ip'];
	$_SESSION['USER_BROWSER'] = $Row['browser'];
	$_SESSION['USER_REGDATE'] = $Row['regdate'];
	$_SESSION['USER_REGIP'] = $Row['regip'];
	$_SESSION['USER_REGBROWSER'] = $Row['regbrowser'];
	$_SESSION['USER_ACTIVE'] = 1;
	if ($_REQUEST['remember']) setcookie('user', $_POST['password'], strtotime('+30 days'), '/');
	MessageSend(3, LANGSYS30, '/profile/');
}
?>
