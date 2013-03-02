<? 
require 'check.php';

$username = $_REQUEST['karl_username'];
$pass = $_REQUEST['karl_pass'];
$groups = $_REQUEST['karl_groups'];
$realname = $_REQUEST['karl_realname'];

if ($username) {
	$hash_sum = computeHash($username, $pass);
	$query = "INSERT INTO Users (username,hash,groups,realname) VALUES ('$username', '$hash_sum', '$groups', '$realname')";
	mysql_query($query);
	echo mysql_error();
	echo "Added.";
}
?>
<form action="add_user.php" method="post">
User: <input name="karl_username"><br>
Pass: <input name="karl_pass" type="password"><br>
Groups: <input name="karl_groups"><br>
Name: <input name="karl_realname"><br>
<input type="submit">
</form>
