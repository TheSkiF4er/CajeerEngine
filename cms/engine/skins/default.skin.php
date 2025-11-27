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
 File: default.skin.php
------------------------------------------------------------
 Use: Skin templates
============================================================
*/

function HEAD ($p1, $p2 = '') {
	if ($p1 == 0) $p1 = home_title;
	else if ($p1 != 0) $p1 = short_title.' | '.$p2;
	if ($_COOKIE['language'] == 'English') $lang = 'en';
	else if ($_COOKIE['language'] == 'French') $lang = 'fr';
	else if ($_COOKIE['language'] == 'German') $lang = 'de';
	else if ($_COOKIE['language'] == 'Italian') $lang = 'it';
	else if ($_COOKIE['language'] == 'Russian') $lang = 'ru';
	else if ($_COOKIE['language'] == 'Spanish') $lang = 'es';
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.charset.'"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>'.$p1.'</title><meta name="description" content="'.description.'"><meta name="keywords" content="'.keywords.'"><meta name="authors" content="'.authors.'"><meta name="copyright" content="'.copyright.'"><meta http-equiv="content-language" content="'.$lang.'"><link rel="stylesheet" href="/engine/skins/stylesheets/styles.css"><script type="text/javascript" src="/engine/skins/javascripts/scripts.js"></script>'.metrics;
}
?>
