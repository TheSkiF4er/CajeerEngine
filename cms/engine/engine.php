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
 File: engine.php
============================================================
*/

include_once ENGINE_DIR.'/skins/'.skin.'.skin.php';
include_once ENGINE_DIR.'/modules/functions.php';

if (!$_SESSION['USER_ACTIVE']) $_SESSION['USER_ACTIVE'] = 0;
if ($_SESSION['USER_ACTIVE'] != 1 and $_COOKIE['user']) {
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `uid`, `login`, `email`, `pnumber`, `password`, `group`, `name`, `surname`, `gender`, `dob`, `ncity`, `city`, `bio`, `company`, `teams`, `languages`, `planguages`, `website`, `projects`, `country`, `lastdate`, `ip`, `browser`, `regdate`, `regip`, `regbrowser` FROM `users` WHERE `password` = '$_COOKIE[user]'"));
	$_SESSION['USER_ID'] = $Row['id'];
	$_SESSION['USER_UID'] = $Row['uid'];
	$_SESSION['USER_LOGIN'] = $Row['login'];
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
}

if ($_SESSION['USER_ACTIVE'] == 1) $User = $_SESSION['USER_LOGIN'];
else $User = 'quest';
if ($User == 'quest') $Online = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `ip` FROM `online` WHERE `ip` = '$_SERVER[REMOTE_ADDR]'"));
else $Online = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `user` FROM `online` WHERE `user` = '$User'"));
if ($Online['ip']) mysqli_query($CONNECT, "UPDATE `online` SET `time` = NOW() WHERE `ip` = '$_SERVER[REMOTE_ADDR]'");
else if ($Online['user'] and $Online['user'] != 'quest') mysqli_query($CONNECT, "UPDATE `online` SET `time` = NOW() WHERE `user` = '$User'");
else mysqli_query($CONNECT, "INSERT INTO `online` SET `ip` = '$_SERVER[REMOTE_ADDR]', `user` = '$User', `time` = NOW()");

if ($_SERVER['REQUEST_URI'] == '/') {
	$Page = 'index';
	$Module = 'index';
} else {
	$URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$URL_Parts = explode('/', trim($URL_Path, ' /'));
	$Page = array_shift($URL_Parts);
	$Module = array_shift($URL_Parts);
	if (!empty($Module)) {
		$Param = array();
		for ($i = 0; $i < count($URL_Parts); $i++) {
			$Param[$URL_Parts[$i]] = $URL_Parts[++$i];
		}
	} else $Module = 'main';
}
/* !PHP Fatal error!
if ($_SESSION['USER_ACTIVE']) {
	if ($Page != 'profile') {
		$Num = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(`id`) FROM `notice` WHERE `status` = 0 AND `uid` = $_SESSION[USER_ID]"));
		if ($Num[0]) MessageSend(3, LANGSYS04.' <a href="/profile/">'.LANGSYS06.' <b>('.$Num[0].')</b> '.LANGSYS05.'</a>', '', 0);
	}
}
*/
if (site_offline == 1 and $REMOTEADDR != adminip) include TEMPLATE_DIR.('/offline.php');
else if ($_SESSION['USER_GROUP'] == -1) include ROOT_DIR.('/templates/banned.php');

else if ($Page == 'index' and $Module == 'index') include TEMPLATE_DIR.('/main.php');
else if ($Page == 'feedback' and $Module == 'main') include TEMPLATE_DIR.('/feedback.php');
else if ($Page == 'account' and $Module == 'main') include TEMPLATE_DIR.('/account.php');
else if ($Page == 'profile' and $Module == 'main') include TEMPLATE_DIR.('/userinfo.php');

else if (in_array($Page, array($StaticPages)) and $Module == 'main') include TEMPLATE_DIR.('/$Page.php');
else if (in_array($Page, array($StaticPages)) and in_array($Module, array($StaticModules))) include TEMPLATE_DIR.('/$Module.php');

else if ($Page == 'search') include TEMPLATE_DIR.('/search.php');
else if ($Page == 'searchresult') include TEMPLATE_DIR.('/searchresult.php');
else if ($Page == 'news') {
	if ($Module == 'main' or $Page == 'news' and $Module == 'category' or $Page == 'news' and $Module == 'main') include TEMPLATE_DIR.('/shortstory.php');
	else if ($Module == 'material') include TEMPLATE_DIR.('/fullstory.php');
}
else if ($Page == 'account' and $Module == 'user') {
	$Param['id'] += 0;
	if ($Param['id']) include TEMPLATE_DIR.('/profile_popup.php');
	else MessageSend(1, LANGSYS01, '/');
}
else if ($Page == 'user' and $Module == 'main') MessageSend(1, LANGSYS01, '/');
else if ($Page == 'user' and $Module) {
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `login` = '$Module'"));
	if (!$Row['login']) MessageSend(1, LANGSYS01, '/');
	else include TEMPLATE_DIR.('/profile_popup.php');
}
else if ($Page == 'panel') {
	if ($Module == 'main') include ENGINE_DIR.('/system/main.php');
	else if ($Module == 'login') include ENGINE_DIR.('/system/admin.php');
	else if ($Module == 'addnews') include ENGINE_DIR.('/system/addnews.php');
	else if ($Module == 'editnews') include ENGINE_DIR.('/system/editnews.php');
}
else if ($Page == 'files') {
	if ($Module == 'main' or $Page == 'files' and $Module == 'category' or $Page == 'files' and $Module == 'main') include TEMPLATE_DIR.('/files.php');
	else if ($Module == 'material') include TEMPLATE_DIR.('/materialfiles.php');
}

else if ($Page == 'account' and $Module == 'register') include TEMPLATE_DIR.('/registration.php');
else if ($Page == 'account' and $Module == 'login') include TEMPLATE_DIR.('/login.php');
else if ($Page == 'account' and $Module == 'recovery') include TEMPLATE_DIR.('/lostpassword.php');
else if ($Page == 'profile' and $Module == 'messenger') include TEMPLATE_DIR.('/pm.php');
else if ($Page == 'profile' and $Module == 'hub') include TEMPLATE_DIR.('/hub.php');
else if ($Page == 'profile' and $Module == 'discussions') include TEMPLATE_DIR.('/discussions.php');
else if ($Page == 'profile' and $Module == 'forum') include TEMPLATE_DIR.('/forum.php');

else if ($Page == 'api') include ENGINE_DIR.('/api/api.class.php');
else if ($Page == 'profile' and $Module == 'edit') include ENGINE_DIR.('/profile.php');
else if ($Page == 'account' and $Module == 'registration') include ENGINE_DIR.('/modules/register.php');
else if ($Page == 'account' and $Module == 'authorization') include ENGINE_DIR.('/modules/sitelogin.php');
else if ($Page == 'account' and $Module == 'lostpassword') include ENGINE_DIR.('/modules/lostpassword.php');

else if ($Page == 'account' and $Module == 'settings' and $_POST['enter']) {
	$_POST['templatesselect'] = FormChars($_POST['templatesselect']);
	$_POST['languagesselect'] = FormChars($_POST['languagesselect']);
	setcookie('template', STemp($_POST['templatesselect']), strtotime('+30 days'), '/');
    setcookie('language', SLang($_POST['languagesselect']), strtotime('+30 days'), '/');
	MessageSend(3, LANGSYS02, '/account/');
}
else if ($Page == 'profile' and $Module == 'exit' and $_SESSION['USER_ACTIVE'] == 1) {
	if ($_COOKIE['user']) {
		setcookie('user', '', strtotime('-30 days'), '/');
		unset($_COOKIE['user']);
	}
	session_unset();
	MessageSend(3, LANGSYS03, '/');
}
else NotFound();
?>
