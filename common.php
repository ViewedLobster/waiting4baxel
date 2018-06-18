<?php
require_once('UserDB.php');

$userDB = new UserDB();

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
	function undo_magic_quotes_gpc(&$array) {
		foreach ($array as &$value) {
			if(is_array($value)) {
				undo_magic_quotes_gpc($value);
			} else {
				$value = stripslashes($value);
			}
		}
	}
	undo_magic_quotes_gpc($_POST);
	undo_magic_quotes_gpc($_GET);
	undo_magic_quotes_gpc($_COOKIE);
}

header("Content-Type: text/html; charset=utf8");

session_start();
?>
