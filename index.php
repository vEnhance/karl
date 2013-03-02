<?
session_start();
require './internal/secret.php';

if (isset($_SESSION['username'])) {
	header("Location: $SITE_ROOT/view_set.php");
}
else {
	header("Location: $SITE_ROOT/login.php");
}
?>
