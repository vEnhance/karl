<?
$TITLE_HEAD = "";
$TITLE_TAIL = "";
require './internal/common.php';
require './internal/header.php';
?>

<?
// If no cid, ragequit
if (!isset($CID)) {
	echo '<div class="warning">You must specify a contest ID.  Please do so using the right navigation box.</div>';
	require './internal/footer.php';
	exit();
}

// Query for contest name
$contest_name = $ALL_CONTESTS[$CID]['name'];
if (!$contest_name) {
	echo '<div class="warning">Invalid contest ID.</div>';
	require './internal/footer.php';
	exit();
}

// Update contest information if applicable
if (isset($_POST['update_info'])) {
	$info = mysql_real_escape_string($_POST['info']);
	$query = "UPDATE Contests SET info='$info' WHERE id=$CID";
	if (!mysql_query($query)) {
		echo mysql_error();
	}
	else {
		echo "<div class=\"success\">Changes saved successfully.</div>" . "\n";
	}
	$OK_CONTESTS[$CID]['info'] = $_POST['info'];
	$_SESSION['OK_contests'][$CID]['info'] = $_POST['info'];
}

// If difficulty is set, filter by difficulty
if (isset($_REQUEST['max_diff']) && isset($_REQUEST['min_diff'])) {
	$DIFFICULTY = $_REQUEST['min_diff'];
	$query = "SELECT * FROM Problems WHERE (cid=$CID AND difficulty >= {$_REQUEST['min_diff']} AND difficulty < {$_REQUEST['max_diff']}) ORDER BY zindex";
}
else if ((isset($_REQUEST['difficulty'])) && ($_REQUEST['difficulty'] !== "")) {
	$DIFFICULTY = $_REQUEST['difficulty'];
	$query = "SELECT * FROM Problems WHERE (cid=$CID AND difficulty=$DIFFICULTY) ORDER BY zindex";
}
else {
	$query = "SELECT * FROM Problems WHERE cid=$CID ORDER BY zindex";
}

// Get problems
$probs = mysql_query($query);
$num_probs = mysql_num_rows($probs); 

// Store them all to an array
$prob_rows = array();
while ($prob_row = mysql_fetch_array($probs)) {
	$prob_rows[] = $prob_row;
}

?>

<? include './internal/buttons.php'; ?>

<div class="entry">
<h1>
	<a href="view_set.php?cid=<? echo $CID; ?>">
		<? echo $contest_name; ?>
	</a>
</h1>

<?
echo "<h2><a href=\"javascript:toggleEditSetHideAll()\";>This set contains <strong>$num_probs</strong> problems.</a>";
if ((isset($DIFFICULTY)) && ($DIFFICULTY !== "")) { echo "  Difficulty set at d=$DIFFICULTY."; } 
echo "</h2>";
?>

<div class="entrywrap">
	<div class="message">
	<a href="javascript:toggleHide('contest_info');" class="hider">Contest Description</a>
	<div id="contest_info" class="hidden">
		<? if ($OK_CONTESTS[$CID]['info']) echo "<div class=\"info math_content\">{$OK_CONTESTS[$CID]['info']}</div>"; ?>
		<a href="javascript:toggleHide('edit_contest_info');" class="hider">Edit</a>
		<div id="edit_contest_info" class="hidden">
			<form action="view_set.php?cid=<? echo $CID; ?>" method="post">
			<? do_print_textarea("info", "Information", "128px", $OK_CONTESTS[$CID]['info']); ?>
			<input type="submit" name="update_info" value="Update">
			</form>
		</div>
	</div>
	<br><br>
	<form action="edit_set.php" method="post">
	<input type="hidden" name="sender_cid" value="<? echo $CID; ?>">
	<table><tbody>
	<?
	$vote_sum = 0;
	$diff_sum = 0;
	foreach ($prob_rows as $i => $prob_row) {
		echo "<tr>" . "\n";
		// Print problems
		$id = $prob_row['id'];
		$this_votes = $prob_row['votes'];
		$comments = $prob_row['comments'];

		echo "<td valign=\"top\">" . "\n";
		echo "<a href=\"view_problem.php?id=$id\" class=\"probnum\">{$prob_row['zindex']}.</a> "; 

		// Print votes and stuff
		if ($this_votes > 0) {
			echo "<br><span class=\"set_vote_count set_vote_count_positive\">+$this_votes</span>&nbsp;&nbsp;";
		}
		else if ($this_votes < 0) {
			echo "<br><span class=\"set_vote_count set_vote_count_negative\">$this_votes</span>&nbsp;&nbsp;";
		}
		echo "</td>" . "\n";
		echo "<td style=\"font-size:10pt;\">";
		echo "<span class=\"set_author_name\">({$prob_row['author']})</span> " . "<span class=\"math_content\">{$prob_row['statement']}</span>" . "\n";
		echo "<br>";
		echo "<div class=\"set_hashtag\">" . "\n";
			echo "<span style=\"float:left;\">" . "\n";
				$num_ca = line_count($comments);
				$num_cb = line_count($prob_row['comments_spoiler']);
				echo "<a href=\"ch_vote.php?id=$id&cid=$CID&difficulty=$DIFFICULTY&delta=1\">(+1)</a>" . "\n";
				echo "<a href=\"ch_vote.php?id=$id&cid=$CID&difficulty=$DIFFICULTY&delta=-1\">(-1)</a>" . "\n";
				echo "<a href=\"javascript:toggleEditSetHideAll()\">(move)</a>" . "\n";
				echo "<a href=\"propose_problem.php?id=$id\">(edit)</a>" . "\n";
				echo "<a href=\"view_problem.php?id=$id\">($num_ca,$num_cb)</a>" . "\n";
			echo "</span>" . "\n";
			echo "<span style=\"float:right;\">" . "\n";
			echo "#({$prob_row['subject']}),
				<a style=\"color:inherit;\" href=\"view_set.php?cid=$CID&difficulty={$prob_row['difficulty']}\">
				d={$prob_row['difficulty']}</a>" . "\n";
			echo "</span>" . "\n";
			echo "<br>" . "\n";
		echo "</div>" . "\n";
		echo "<div class=\"set_quick_comments math_content\">";
		if ($line_index = strrpos($comments, "\r\n")) { echo '... ' . substr($comments, $line_index+2); }
		else { echo $comments; }
		echo "</div>";
		echo "<span class=\"edit_set_hidden\">" . "\n";
			echo "<input name=\"old_zindex[$id]\" type=\"hidden\" value=\"{$prob_row['zindex']}\">";
			echo "<input name=\"old_difficulty[$id]\" type=\"hidden\" value=\"{$prob_row['difficulty']}\">";
			echo "Index: <input name=\"new_zindex[$id]\" type=\"text\" value=\"{$prob_row['zindex']}\" size=\"7\">";
			echo str_repeat("&nbsp;", 4);
			echo "Diff: <input name=\"new_difficulty[$id]\" type=\"text\" value=\"{$prob_row['difficulty']}\" size=\"7\">";
			echo str_repeat("&nbsp;", 4);
			echo "Move: <input type=\"checkbox\" name=\"need_move[]\" value=\"$id\">";
			echo str_repeat("&nbsp;", 4);
			echo "ID: $id";
			echo str_repeat("&nbsp;", 4);
			echo "<input type=\"submit\" name=\"submit\" value=\"Update\">";
		echo "</span>" . "\n";
		echo "</td>" . "\n";
		echo "</tr>" . "\n";
		// Tally up
		$vote_sum += $this_votes;
		$diff_sum += $prob_row['difficulty'];
	}
	?>
	</tbody></table>
	</div>
	<script>
	function toggleEditSetHideAll() {
		var elms = document.getElementsByClassName('edit_set_hidden');
		for (var i=0; i<elms.length; i++) {
			elm = elms[i];
			elm.style.visibility = (elm.style.visibility == 'hidden') ? 'visible' : 'hidden';
		}
	}
	</script>
	<div class="efooter">
		<div class="actions">
		<ul><a class="post-replies" href="javascript:toggleEditSetHideAll();">Edit Set</a></ul>
		</div>
		<span class="addthis_button" style="text-align: right;">
		<?	
		if ($num_probs > 0) {
			$avg_diff = round($diff_sum / $num_probs, 2);
		}
		else {
			$avg_diff = "*";
		}
		echo "Total Votes: <b>$vote_sum</b><br>";
		echo "Average Difficulty: <b>$avg_diff</b>";
		?>
		</span>
	</div>
</div>
</div>
<span class="edit_set_hidden">
	<div class="info">
	Use this form to re-order problems and/or move them to a different contest. <br> 
	<br>
	<script>
	function checkAll(value) {
		$('[name="need_move[]"]').each(function() { this.checked = value; });
	}
	</script>
	<a href="javascript:checkAll(true)">Check All</a> :: <a href="javascript:checkAll(false)">Uncheck All</a>
	<br>

	<span class="form_input_name">Move to:</span> <? do_print_selector("target_cid", $CID); ?>
	<input type="submit" name="submit" value="Make Changes"><br>
	<input type="checkbox" name="please_sort_by_difficulty" value="1">Re-sort by difficulty<br>
	<input type="checkbox" name="please_trash_instead" value="1">Delete instead of move<br>
	</div>
	
	<div class="warning">
	There is no confirmation dialog!  Please double check before pressing submit!
	</div>
</span>
</form>

<? require './internal/footer.php'; ?>
