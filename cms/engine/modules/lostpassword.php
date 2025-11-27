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
 File: lostpassword.php
-----------------------------------------------------
 Use: Forgotten password recovery
============================================================
*/

ULogin(0);
if ($Module == 'lostpassword' and !$Param['code'] and substr($_SESSION['RESTORE'], 0, 4) == 'wait') MessageSend(3, LANGSYS13.' <b>'.HideEmail(substr($_SESSION['RESTORE'], 5)).'</b>', '/account/');
if ($Module == 'lostpassword' and $_SESSION['RESTORE'] and substr($_SESSION['RESTORE'], 0, 4) != 'wait') MessageSend(1, LANGSYS14.' <b>'.$_SESSION['RESTORE'].'</b>', '/account/');
if ($Module == 'lostpassword' and $_POST['enter']) {
    //check_antibot($_POST['antibot']);
    //if ($_SESSION['antibot'] != md5($_POST['antibot'])) MessageSend(1, LANGSYS16);
    sleep(5);
    check_login($_POST['login']);
    $_POST['email'] = FormChars($_POST['email']);
    if (!$_POST['login'] or !$_POST['email']/* or !$_POST['antibot']*/) MessageSend(1, LANGSYS15);
    $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `email` FROM `users` WHERE `login` = '$_POST[login]'"));
    if (!$Row['login']) MessageSend(1, LANGSYS01, '/account/');
    mail($Row['email'], 'Cajeer Team', 'Recovery Link: https://cajeer.ru/account/lostpassword/code/'.md5($Row['email']).$Row['id'], 'From: admin@cajeer.ru');
    $_SESSION['RESTORE'] = 'wait_'.$Row['email'];
    MessageSend(3, LANGSYS17.' <b>'.HideEmail($Row['email']).'</b> '.LANGSYS18, '/account/');
}
if ($Module == 'lostpassword' and $Param['code']) {
    $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, 'SELECT `login`, `email` FROM `users` WHERE `id` = '.str_replace(md5($Row['email']), '', $Param['code'])));
    if (!$Row['login']) MessageSend(1, LANGSYS19, '/account/');
    $Random = RandomString(16);
    $_SESSION['RESTORE'] = $Random;
    mysqli_query($CONNECT, "UPDATE `users` SET `password` = '".GenPass($Random, $Row['login'])."' WHERE `login` = '$Row[login]'");
    MessageSend(3, LANGSYS20.' <b>'.$Random.'</b>', '/account/login/');
}
?>
