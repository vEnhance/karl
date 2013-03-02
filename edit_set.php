<?
$TITLE_HEAD = "Editing ";
$TITLE_TAIL = "";

require './internal/common.php';
require './internal/header.php';

if (!isset($_POST['submit'])) {
	echo '<div class="warning">NO YOU</div>';
	require './internal/footer.php';
	exit();
}

$sender_cid = intval($_POST['sender_cid']);
$target_cid = intval($_POST['target_cid']);

if ($_POST['please_trash_instead'] == 1) {
	$target_cid = -1;
}

$CID = $sender_cid;

if (!isset($_POST['need_move'])) {
	if ($target_cid != $sender_cid) {
		echo "<div class=\"error\">Cannot perform operation: no problems specified.  $target_cid, $sender_cid</div>";
		require './internal/footer.php';
		exit();
	}
	else {
		$need_move_ids = array();
		$recipent_exists = false;
	}
}
else {
	if ($target_cid == $sender_cid) {
		echo "<div class=\"warning\">You are moving a set to itself.</div>";
		require './internal/footer.php';
		exit();
	}
	$need_move_ids = $_POST['need_move'];
}


// Construct an array of z-indices for each contest.
$sender_contest_user_zindices = array();
$target_contest_user_zindices = array();
foreach ($_POST['new_zindex'] as $this_id => $this_user_zind) {
	if (in_array($this_id, $need_move_ids)) {
		$target_contest_user_zindices[$this_id] = floatval($this_user_zind);
	}
	else {
		$sender_contest_user_zindices[$this_id] = floatval($this_user_zind);
	}
}
echo "<br>";

// Sort each of these arrays
if (isset($_REQUEST['please_sort_by_difficulty'])) {
	function cmp($id1, $id2) {
		$diffs = $_POST['new_difficulty'];
		if ($diffs[$id1] != $diffs[$id2]) return intval($diffs[$id1]) - intval($diffs[$id2]);
		else return intval($_POST['new_zindex'][$id1]) - intval($_POST['new_zindex'][$id2]);
	}
	uksort($sender_contest_user_zindices, 'cmp');
}
else {
	asort($sender_contest_user_zindices);
}
asort($target_contest_user_zindices);

// Update the z_index of the sender contest.
$sender_true_zindex = 0;
foreach ($sender_contest_user_zindices as $this_id => $this_user_zind) {
	$sender_true_zindex++;
	if ($sender_true_zindex == $_REQUEST['old_zindex'][$this_id]) { continue; }
	$query = "UPDATE Problems SET zindex=$sender_true_zindex WHERE id=$this_id";
	$result = mysql_query($query);
	if (!$result) {
		echo '<div id="error">' . mysql_error() . "</div>";
		echo '<div id="info">' . "Here was the query: " .  $query . "</div>";
		require './internal/footer.php';
		exit();
	}
}

// Get zindex of target contest
$target_true_zindex = mysql_num_rows(mysql_query("SELECT * FROM Problems WHERE cid=$target_cid"));
foreach ($target_contest_user_zindices as $this_id => $this_user_zind) {
	$target_true_zindex++;
	$query = "UPDATE Problems SET zindex=$target_true_zindex, cid=$target_cid WHERE id=$this_id";
	$result = mysql_query($query);
	if (!$result) {
		echo '<div id="error">' . mysql_error() . "</div>";
		echo '<div id="info">' . "Here was the query: " .  $query . "</div>";
		require './internal/footer.php';
		exit();
	}
}

// Finally, update difficulties, if any.
foreach ($_REQUEST['new_difficulty'] as $this_id => $this_difficulty) {
	if ($this_difficulty == $_REQUEST['old_difficulty'][$this_id]) { continue; }
	$query = "UPDATE Problems SET difficulty=$this_difficulty WHERE id=$this_id";
	$result = mysql_query($query);
	if (!$result) {
		echo '<div id="error">' . mysql_error() . "</div>";
		echo '<div id="info">' . "Here was the query: " .  $query . "</div>";
		require './internal/footer.php';
		exit();
	}
}

?>

<div class="success">
All changes saved.
<br><br>
<?
if ($target_cid != $sender_cid) {
	echo "Click <a href=\"view_set.php?cid=$sender_cid\">here</a> to return to the originating problem set.<br>";
	echo "Click <a href=\"view_set.php?cid=$target_cid\">here</a> to return to the recipient problem set.<br>";
}
else {
	echo "Click <a href=\"view_set.php?cid=$sender_cid\">here</a> to return to the problem set.";
}
?>
</div>

<?
require './internal/footer.php';
?>
