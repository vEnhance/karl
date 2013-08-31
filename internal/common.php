<?
date_default_timezone_set('America/Los_Angeles');
require 'secret.php';

function stripslashesFromArray($value) {
    $value = is_array($value) ?
                array_map('stripslashesFromArray', $value) :
                stripslashes($value);

    return $value;
}
if (get_magic_quotes_gpc()) {
	$_GET = stripslashesFromArray($_GET);
	$_POST = stripslashesFromArray($_POST);
	$_COOKIE = stripslashesFromArray($_COOKIE);
	$_REQUEST = stripslashesFromArray($_REQUEST);
}


function computeHash($user, $pass) {
	$PASSWORD_SALT = constant("PASSWORD_SALT");
	$hash_sum = sha1("user=" . $user . ", pass=" . $PASSWORD_SALT . $pass);
	return $hash_sum;
}


// Start session
if (session_id() == "") {
	session_start();
}

// Open MySQL Connection
$conn = mysql_connect("localhost", $DB_USER, $DB_PASS);
if (!$conn) {
	die('Could not connect: ' . mysql_error());
}
mysql_select_db($DB_NAME, $conn);


// Get login information
if (!isset($NEEDS_LOGIN)) {
	$NEEDS_LOGIN = 1;
}

if (!$NEEDS_LOGIN) {
	// don't do anything.
}
else {
	// Get config vars
	$USER = $_SESSION['username'];
	$USER_DATA = $_SESSION['userdata'];
	$OK_CONTESTS = $_SESSION['OK_contests'];
	$ALL_CONTESTS = $_SESSION['all_contests'];

	// Send to login page if user is not set.
	if (!$USER) {
		$_SESSION['page_redir_to'] = $_SERVER['REQUEST_URI'];
		header("Location: $SITE_ROOT/login.php");
		exit;
	}

	function do_auth_error() {
		global $TITLE_HEAD, $CID, $ID, $USER, $conn;
		$TITLE_HEAD = "Authorization Denied";
		$CID = "";
		$ID = "";
		require './internal/header.php';
		echo "<div class=\"error\">You are not authorized to view this page.<br><br>You are currently logged in as $USER.</div>";
		require './internal/footer.php';
		die();
	}

	// Get ID and such
	if (isset($_REQUEST['id'])) {
		$ID = intval($_REQUEST['id']);
		if ($ID != -1) { 
			// Get problem row
			$query = "SELECT * FROM Problems WHERE id=$ID";
			$result = mysql_query($query);
			if (mysql_num_rows($result) < 1) {
				$TTILE_HEAD = "Not Found";
				require './internal/header.php';
				echo '<div class="error">Could not find the requested problem.</div>';
				require './internal/footer.php';
				die();
			}
			$PROB_ROW = mysql_fetch_array($result);
			$FILL_IN = $PROB_ROW;

			$CID = $PROB_ROW['cid'];
			$DIFFICULTY = $PROB_ROW['difficulty'];

			if (!array_key_exists($CID, $_SESSION['all_contests'])) {
				do_auth_error();
			}
		}
	}
	if (isset($_REQUEST['cid'])) {
		$CID = intval($_REQUEST['cid']);
		if (!array_key_exists($CID, $_SESSION['all_contests'])) {
			do_auth_error();
		}

	}
}
?>
