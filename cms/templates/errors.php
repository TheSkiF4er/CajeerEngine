<?php
if ($_SESSION['USER_ERROR'] == 404) {
	$_SESSION['USER_ERROR'] = '';
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>404 Not Found.</title></head><body></body></html>';
}
else if ($_SESSION['USER_ERROR'] == 403) {
	$_SESSION['USER_ERROR'] = '';
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>403 Forbidden.</title></head><body></body></html>';
}
?>
