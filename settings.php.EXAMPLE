<?
$TITLE_HEAD = "Settings";
$TITLE_TAIL = "";
require './internal/common.php';

$messages = "";

if (isset($_POST['submit'])) {
	$to_change = array(
		'realname' => true,
		'use_ace' => false,
		'ace_theme' => true,
		'css' => false,
	);
	$some_error = 0;
	foreach ($to_change as $key => $is_string) {
		$val = mysql_real_escape_string($_POST[$key]);
		if ($is_string)
			$query = "UPDATE Users SET $key='$val' WHERE username='$USER'";
		else
			$query = "UPDATE Users SET $key=$val WHERE username='$USER'";
		$res = mysql_query($query);
		$error = mysql_error();
		if (!$res) {
			$messages .= "<div class=\"warning\">Error occured while updating $key</div>";
			$some_error = 1;
		}
	}
	if (!$some_error) {
		$messages .= "<div class=\"success\">Changes saved successfully.</div>";
	}
	// Get new userdata
	$_SESSION['userdata'] = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE username='$USER'"));
	$USER_DATA = $_SESSION['userdata'];
}
if (isset($_POST['pw_change'])) {
	if ($_POST['new_password1'] != $_POST['new_password2']) {
		$messages .= "<div class=\"warning\">Passwords do not match.  No changes made.</div>";
	}
	else if (computeHash($USER, $_POST['orig_password']) != $_SESSION['userdata']['hash']) {
		$messages .= "<div class=\"warning\">The password you specified is incorrect.</div>";
	}
	else {
		$new_hash = computeHash($USER, $_POST['new_password1']);
		$query = "UPDATE Users SET hash='$new_hash' WHERE username='$USER'";
		if (mysql_query($query)) {
			$messages .= "<div class=\"success\">Password changed successfully</div>";
		}
		else {
			$messages .= "<div class=\"error\">Some error occured:<br>" . mysql_error() . "</div>";
		}
	}
}
$FILL_IN = $_SESSION['userdata'];

require './internal/header.php';
echo $messages;
?>
<div class="entry">
<h1>Settings</h1>
	<div class="entrywrap">
	<div class="message">
	<form action="settings.php" method="post">
	<table><tbody>
	<tr><? do_print_text("css", "CSS"); ?></tr>
	<tr><? do_print_text("realname", "IRL Name"); ?></tr>
	<tr><? do_print_text("use_ace", "Use Ace? (1/0)"); ?></tr>
	<tr><? do_print_text("ace_theme", "Ace Theme"); ?></tr>
	</tbody></table>
	<input type="submit" name="submit" value="Go">
	</form>
	</div>
	</div>
</div>
<div class="entry">
<h1>Change Password</h1>
	<div class="entrywrap">
	<div class="message">
	<form action="settings.php" method="post">
	Enter your old password:<br>
	<input type="password" name="orig_password">
	<br>
	Enter your new password twice:<br>
	<input type="password" name="new_password1"><br>
	<input type="password" name="new_password2"><br>
	<input type="submit" name="pw_change" value="Change">
	</form>
	</div>
	</div>
</div>
<div class="entry">
<h1>All Contests</h1>
	<div class="entrywrap">
	<div class="message">
	<ul>
	<?
	foreach ($ALL_CONTESTS as $this_cid => $this_row) {
		echo "<li><a href=\"/view_set.php?cid=$this_cid\">{$this_row['name']}</a></li>";
	}
	?>
	</ul>
	</div>
	</div>
</div>

<?
require './internal/footer.php';
?>
