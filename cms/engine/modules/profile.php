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
 File: profile.php
-----------------------------------------------------
 Use: profile
============================================================
*/

ULogin(1);
if ($Module == 'edit' and $_POST['enter']) {
	$_POST['login'] = FormChars($_POST['login']);
	$_POST['email'] = FormChars($_POST['email']);
	$_POST['tel'] = FormChars($_POST['tel']);
	$_POST['name'] = FormChars($_POST['name']);
	$_POST['surname'] = FormChars($_POST['surname']);
	$_POST['gen'] = FormChars($_POST['gen']);
	$_POST['city'] = FormChars($_POST['city']);
	$_POST['bio'] = FormChars($_POST['bio']);
	$_POST['company'] = FormChars($_POST['company']);
	$_POST['teams'] = FormChars($_POST['teams']);
	$_POST['lang'] = FormChars($_POST['lang']);
	$_POST['plang'] = FormChars($_POST['plang']);
	$_POST['wsite'] = FormChars($_POST['wsite']);
	$_POST['projects'] = FormChars($_POST['projects']);
	$_POST['country'] = FormChars($_POST['country']);
	$_POST['opassword'] = FormChars($_POST['opassword']);
	$_POST['npassword'] = FormChars($_POST['npassword']);
	//$_POST['antibot'] = FormChars($_POST['antibot']);
	if ($_POST['login'] != $_SESSION['USER_LOGIN']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `login` = '$_POST[login]' WHERE `uid` = '$_SESSION[USER_UID]'");
		$_SESSION['USER_LOGIN'] = $_POST['login'];
	}
	if ($_POST['email'] != $_SESSION['USER_EMAIL']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `email` = '$_POST[email]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_EMAIL'] = $_POST['email'];
	}
	if ($_POST['tel'] != $_SESSION['USER_PNUM']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `pnumber` = '$_POST[tel]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_PNUM'] = $_POST['tel'];
	}
	if ($_POST['name'] != $_SESSION['USER_NAME']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `name` = '$_POST[name]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_NAME'] = $_POST['name'];
	}
	if ($_POST['surname'] != $_SESSION['USER_SURNAME']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `surname` = '$_POST[surname]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_SURNAME'] = $_POST['surname'];
	}
	if ($_POST['gen'] != $_SESSION['USER_GEN']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `gender` = '$_POST[gen]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_GEN'] = $_POST['gen'];
	}
	if ($_POST['city'] != $_SESSION['USER_CITY']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `city` = '$_POST[city]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_CITY'] = $_POST['city'];
	}
	if ($_POST['bio'] != $_SESSION['USER_BIO']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `bio` = '$_POST[bio]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_BIO'] = $_POST['bio'];
	}
	if ($_POST['company'] != $_SESSION['USER_COM']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `company` = '$_POST[company]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_COM'] = $_POST['company'];
	}
	if ($_POST['teams'] != $_SESSION['USER_TEAM']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `teams` = '$_POST[teams]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_TEAM'] = $_POST['teams'];
	}
	if ($_POST['lang'] != $_SESSION['USER_LANG']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `languages` = '$_POST[lang]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_LANG'] = $_POST['lang'];
	}
	if ($_POST['plang'] != $_SESSION['USER_PLANG']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `planguages` = '$_POST[plang]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_PLANG'] = $_POST['plang'];
	}
	if ($_POST['wsite'] != $_SESSION['USER_WSITE']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `website` = '$_POST[wsite]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_WSITE'] = $_POST['wsite'];
	}
	if ($_POST['projects'] != $_SESSION['USER_PROJECTS']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `projects` = '$_POST[projects]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_PROJECTS'] = $_POST['projects'];
	}
	if ($_POST['country'] != $_SESSION['USER_COUNTRY']) {
		mysqli_query($CONNECT, "UPDATE `users` SET `country` = '$_POST[country]' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_COUNTRY'] = $_POST['country'];
	}
	if ($_POST['opassword'] or $_POST['npassword']) {
		if (!$_POST['opassword']) MessageSend(1, LANGSYS21);
		if (!$_POST['npassword']) MessageSend(1, LANGSYS22);
		if ($_SESSION['USER_PASSWORD'] != GenPass($_POST['opassword'], $_SESSION['USER_LOGIN'])) MessageSend(1, LANGSYS23);
		$Password = GenPass($_POST['npassword'], $_SESSION['USER_LOGIN']);
		mysqli_query($CONNECT, "UPDATE `users` SET `password` = '$Password' WHERE `login` = '$_SESSION[USER_LOGIN]'");
		$_SESSION['USER_PASSWORD'] = $Password;
	}
	MessageSend(3, LANGSYS24);
}
?>
