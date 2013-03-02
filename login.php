<?
$NEEDS_LOGIN = 0;
$TITLE_HEAD = "Login";
$TITLE_TAIl = "";
require './internal/common.php';

$error_messages = "";

if (isset($_POST['page_redir_to'])) {
	$redir_url = $_REQUEST['page_redir_to'];
}
$redir_url = $_SESSION['page_redir_to'];

if (isset($_REQUEST['karl_username'])) {
	$user = mysql_real_escape_string($_REQUEST['karl_username']);
	$pass = $_REQUEST['karl_password'];

	// Get user information
	$query = "SELECT * FROM Users WHERE username='$user'";
	$USER_DATA = mysql_fetch_array(mysql_query($query));
	$hash_sum = computeHash($user, $pass);
	if (!$USER_DATA) {
		$error_messages .= "<div class=\"warning\">No such user found.</div>";
	}
	else if ($hash_sum != $USER_DATA['hash']) {
		$error_messages .= "<div class=\"warning\">Incorrect password.</div>";
	}
	else {
		// Get ok contests
		if ($USER_DATA['groups'] == 'admin') {
			$avail_contest_rows = mysql_query("SELECT * FROM Contests WHERE allow <> 1 ORDER BY except,name");
		}
		else {
			$usergroups = $USER_DATA['groups'];
			$blah = "(";
			foreach (explode(',',$usergroups) as $group) {
				$blah .= "'$group',";
			}
			$blah .= "'$user')";
			$avail_contest_rows = mysql_query("SELECT * FROM Contests WHERE allow <> 1 AND except IN $blah ORDER BY except,name");
		}
		while ($row = mysql_fetch_array($avail_contest_rows)) {
			$OK_CONTESTS[$row['id']] = $row;
		}
		$_SESSION['OK_contests'] = $OK_CONTESTS;
		$ALL_CONTESTS = $OK_CONTESTS;
		$more_contest_rows = mysql_query("SELECT * FROM Contests WHERE allow=1");
		while ($row = mysql_fetch_array($more_contest_rows)) {
			$ALL_CONTESTS[$row['id']] = $row;
		}
		$_SESSION['all_contests'] = $ALL_CONTESTS;
		$_SESSION['userdata'] = $USER_DATA;
		$_SESSION['username'] = $user;
		$USER = $user;
		$error_messages .= "<div class=\"success\">Login successful.  You may now navigate using the links at the right now.</div>";

		// if somewhere to redirect to
		if ($_POST['page_redir_to']) {
			header("Location: $SITE_ROOT{$_POST['page_redir_to']}");
			exit;
		}
	}
}
else {
	// logout of stuff
	session_destroy();
}

require './internal/header.php';
echo $error_messages;

?>

<h1>Login</h1>
<form action="login.php" method="post">
<input name="page_redir_to" type="hidden" value="<? echo $redir_url; ?>">
<table><tbody>
<tr><? do_print_text("karl_username", "Username", "", 20); ?></tr>
<tr><? do_print_text("karl_password", "Password", "", 20, "password"); ?></tr>
<tr><td colspan="2"><input type="submit" name="karl_login" value="Login"></td></tr>
</tbody></table>
</form>

<?
require './internal/footer.php';
?>

