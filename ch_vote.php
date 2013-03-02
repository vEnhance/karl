<?
require './internal/common.php';

$delta = intval($_GET['delta']);

$query = "UPDATE Problems SET votes=votes+$delta WHERE id=$ID";
if (!mysql_query($query)) { die("Error"); }

if (isset($_GET['cid'])) {
	if ($_GET['difficulty']) {
		header("Location: {$SITE_ROOT}/view_set.php?cid={$_GET['cid']}");
	}
	else {
		header("Location: {$SITE_ROOT}/view_set.php?cid={$_GET['cid']}&difficulty={$_GET['difficulty']}&dsubmit=1");
	}
}
else {
	header("Location: {$SITE_ROOT}/view_problem.php?id=$ID");
}

?>
