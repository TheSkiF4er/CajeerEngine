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
 File: api.class.php
------------------------------------------------------------
 Use: API
============================================================
*/

function Error ($p1, $p2) {
	exit('{"error:"'.$p1.', "text":"'.$p2.'"}');
}
if ($Module == 'users') {
	if (!$Param['login']) Error(1, 'User login not specified.');
	if (!$Param['param']) Error(2, 'Method parameter(s) not specified.');
	$Param['login'] = FormChars($Param['login']);
	$Array = array('name', 'regdate', 'uid');
	$Exp = explode('.', $Param['param']);
	foreach ($Exp as $key) if ($Param['param'] != 'all' and !in_array($key, $Array)) Error(3, 'The parameter is incorrect.');
	if ($Param['param'] == 'all') $Select = $Array;
	else $Select = $Exp;
	foreach ($Select as $key) $SQL .= "`$key`,";
	$SQL = substr($SQL, 0, -1);
	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT $SQL FROM `users` WHERE `login` = '$Param[login]'"));
	if (!$Row) Error(4, 'User specified incorrectly.');
	echo json_encode($Row, JSON_UNESCAPED_UNICODE);
} else Error(0, 'No method specified.');
?>
