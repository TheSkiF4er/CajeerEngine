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
 File: search.php
-----------------------------------------------------
 Use: search
============================================================
*/

function SearchForm () {
	global $Page;
	echo '<form class="form" method="POST" action="/search/'.$Page.'"><p><input type="text" name="text" class="feedback-input" value="'.$_SESSION['SEARCH'].'" placeholder="'.LANGSYS35.'" id="search" required></p><input type="submit" value="'.LANGSITE50.'" class="button-submit" name="enter"><div class="ease"></div></form>';
}
?>
