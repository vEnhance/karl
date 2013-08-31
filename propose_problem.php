<?
$TITLE_HEAD = "Editing ";
$TITLE_TAIL = "";
require './internal/common.php';

do if (isset($_POST['submit'])) {
	$FILL_IN = $_POST;
	$curr_time = time();

	// $ID = mysql_real_escape_string($_POST['id']);
	// $CID = mysql_real_escape_string($_POST['cid']);

	$difficulty = (int) $_POST['difficulty'];
	$is_new_problem = false;

	if ($ID == -1) {
		// New problem.
		$is_new_problem = true;
		$zindex = (int) $_POST['zindex'] + 1;
		
		// Udpate old problems
		$query = "UPDATE Problems SET zindex=zindex+1 WHERE cid=$CID AND zindex>=$zindex ORDER BY zindex";
		$above_result = mysql_query($query);
		if (!$above_result) { break; }

		// Insert new problem
		$query = "INSERT INTO Problems (time_last_update, time_submit, zindex) VALUES ($curr_time, $curr_time, $zindex)";
		if (!mysql_query($query)) {
			break;
		}
		$ID = mysql_insert_id();
	}

	// Update attributes
	$to_change = array(
		'statement' => true,
		'author' => true,
		'answer' => true,
		'subject' => true,
		'solution' => true,
		'comments' => true,
		'comments_spoiler' => true,
		'title' => true,
		'difficulty' => false,
		'cid' => false);
	foreach($to_change as $key => $is_string) {
		if (!isset($_POST[$key])) { continue; }
		$val = mysqli_real_escape_string($_POST[$key]);

		// Handle blank values and such
		if ((!$is_string) && (!$val)) {
			if ($is_new_problem) { $val = 0; }
			else { continue; }
		}

		if ($is_string) { $query = "UPDATE Problems SET $key='$val' WHERE id=$ID"; }
		else { $query = "UPDATE Problems SET $key=$val WHERE id=$ID"; }
		if (!mysql_query($query)) { break 2; }
	}

	// Change most recent update/comment time.
	if (isset($_POST['is_comment']) || ($is_new_problem && ($_POST['comments'] || $_POST['comments_spoiler']))) {
		$query = "UPDATE Problems SET time_last_comment=$curr_time WHERE id=$ID";
		if (!mysql_query($query)) { break; }
	}
	else {
		$query = "UPDATE Problems SET time_last_update=$curr_time WHERE id=$ID";
		if (!mysql_query($query)) { break; }
	}


	// If successful, redirect to newly added problem.
	header("Location: " . $SITE_ROOT . "/view_problem.php?id=$ID&success=1");
} while (false);
// This idiom lets us break.


require './internal/header.php';
if (mysql_error()) {
	echo "<div class=\"error tex2jax_ignore\">" . mysql_error() . "</div>" . "\n";
	echo "<div class=\"info tex2jax_ignore\">" . "Here was your query: <br>" . $query . "</div>";
	$FILL_IN = $_POST;
}
else if (!isset($ID) || ($ID == -1)) {
	$ID = -1;
	$FILL_IN = $_POST;
	if (isset($CID)) {
		$FILL_IN['cid'] = $CID;
	}
	else {
		$FILL_IN['cid'] = $_COOKIE['cookie_last_cid'];
	}
	if (isset($DIFFICULTY)) {
		$FILL_IN['difficulty'] = $DIFFICULTY;
	}
	if ($_SESSION['userdata']['realname']) {
		$FILL_IN['author'] = $_SESSION['userdata']['realname'];
	}
}
else {
	if (!$PROB_ROW) {
		$FILL_IN = $_POST;
		echo "<div class=\"error\"><b>Error</b>: Cannot find problem id=$ID</div>";
	}
	else {
		$FILL_IN = $PROB_ROW;
		echo "<a href=\"view_problem.php?id=$ID\">&lt; Back</a>";
	}
	$DIFFICULTY = $result['difficulty'];
}

?>

<form action="propose_problem.php" method="post">
	<input type="hidden" name="id" value="<?
		if (isset($_GET['id'])) echo $_GET['id'];
		else echo -1;
		?>">
	<?
		if (isset($_GET['id'])) {
			do_print_submit();
		}
		else {
			echo '<table><tr>';
			echo '<td class="form_input_name">Contest</td>';
			echo '<td>';
			echo do_print_selector("cid", $_GET['cid']);
			echo '</td>';
			echo '<td>';
			do_print_text("zindex", "Insert After", 0, 5);
			echo '</td>';
			echo '<td>';
			do_print_submit();
			echo '</td>';
			echo "</tr>";
			echo "</table>" . "\n";
		}
		echo '<table><tbody><tr>';
		do_print_text("title", "Title");
		echo '</tr></tbody></table>';
		do_print_textarea("statement", "Statement");
		echo "<table><tbody>" . "\n";
		echo "<tr>";
		do_print_text("author", "Author");
		do_print_text("answer", "Answer");
		echo "</tr>" . "\n";
		echo "<tr>";
		do_print_text("subject", "Subject");
		do_print_text("difficulty", "Difficulty");
		echo "</tr>" . "\n";
		echo "</tbody></table>" . "\n";
	?>

	<br />
	<?
		do_print_textarea("solution", "Solution");
		do_print_textarea("comments", "Comments A");
		do_print_textarea("comments_spoiler", "Comments B");
		do_print_submit();
	?>
	</tbody>
	</table>
</form>


<? require './internal/footer.php'; ?>
