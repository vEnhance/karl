</div>

<div id="side">
<?
if (isset($USER)) {
	echo "<span style=\"font-size:11pt;\">Logged in as <b>$USER</b>.  <a href=\"/login.php\">(logout)</a></span>";
}

if (!isset($CID)) {
	$CID = $_COOKIE['cookie_last_cid'];
}
?>

<form id="footer_form_diff_range" action="view_set.php" method="get">
<table>
	<tbody>
	<tr>
		<td class="form_input_name">Contest</td>
		<td><? if (isset($USER)) { echo do_print_selector("footer_cid", $CID); } else { echo '<select></select>'; } ?></td>
		<td><a href="javascript:$('select[name=footer_cid]').trigger('change')">Go</a></td>
	</tr>
	<tr>
		<? do_print_text("footer_difficulty", "Difficulty", $DIFFICULTY, 5); ?>
		<td><a href="javascript:$('select[name=footer_difficulty]').trigger('change')">Go</a></td>
	</tr>
	<tr>
		<td class="form_input_name">Range</td>
		<td><input class="footer_form_diff_range_input" type="text" name="min_diff" size="5"> to <input class="footer_form_diff_range_input" type="text" name="max_diff" size="5"></td>
		<td></td>
		<input type="hidden" name="cid" value="<? echo $CID; ?>">
	</tr>
	<tr>
		<td colspan="3">
		<h4 class="footer_settings_links">
			<a href="propose_problem.php?cid=<? echo $CID; if ($DIFFICULTY != "") echo "&difficulty=$DIFFICULTY"; ?>">Propose New Problem</a><br>
			<a href="/settings.php">Settings and Past Contests</a>
		</h4></td>
	</tr>
	</tbody>
</table>
</form>

<script type="text/javascript">
$("select[name=footer_cid]").change(function() {
	window.location = "<? echo $SITE_ROOT; ?>/view_set.php?cid=" + this.value;
})
$("input[name=footer_difficulty]").change(function() {
	if (this.value) {
		window.location = "<? echo $SITE_ROOT; ?>/view_set.php?cid=<? echo $CID; ?>&difficulty=" + this.value;
	}
	else {
		window.location = "<? echo $SITE_ROOT; ?>/view_set.php?cid=<? echo $CID; ?>";
	}
})
$(".footer_form_diff_range_input").change(function() {
	var boss = this.form;
	if (boss.elements[0].value && boss.elements[1].value) {
		boss.submit();
	}
})
// Set recent cookies
setCookie('cookie_last_cid', '<? echo $CID; ?>');
</script>

<?
function do_print_recent($var_name, $header_name, $search_limit=7) {
	echo "<h3>$header_name</h3>" . "\n";
	if (!isset($_SESSION["recent_$var_name"]) || (isset($_REQUEST["reload_recent_$var_name"]))) {
		global $OK_CONTESTS;
		$OK_contests_imploded_list = implode(",", array_keys($OK_CONTESTS));
		$query = "SELECT cid,difficulty,id,topic,zindex,$var_name FROM Problems WHERE cid IN ($OK_contests_imploded_list) ORDER BY $var_name DESC LIMIT $search_limit";
		$recent_comment_result = mysql_query($query);
		$res = "<table class=\"recent_problems\">" . "\n";
		$res .= "<colgroup>" . "\n";
		$res .= "<col width=\"20px;\">" . "\n";
		$res .= "<col width=\"150px;\">" . "\n";
		$res .= "<col width=\"100px;\">" . "\n";
		$res .= "</colgroup>" . "\n";
		$res .= "<tbody>" . "\n";

		while ($recent_row = mysql_fetch_array($recent_comment_result)) {
			$recent_time = $recent_row[$var_name];
			// Select a suitable date format
			if (date("F j, Y", $recent_time) == date("F j, Y", time())) {
				$string_time = date("g:i a", $recent_time); // within today
				$string_adj = "at";
			}
			else if (date("Y", $recent_time) == date("Y", time())) {
				$string_time = date("M j", $recent_time); // within this year
				$string_adj = "on";
			}
			else {
				$string_time = date("M j Y", $recent_time);
				$string_adj = "on";
			}
			$res .= "<tr><td style=\"text-align:right;\" valign=\"top\"><a href=\"view_set.php?cid={$recent_row['cid']}&difficulty={$recent_row['difficulty']}\">{$recent_row['zindex']}.</a></td>
				<td><a href=\"view_problem.php?id={$recent_row['id']}\">{$recent_row['topic']}</a></td><td valign=\"top\">$string_time</td></tr>" . "\n";
		}
		$res .= "</tbody></table>" . "\n";
		$_SESSION["recent_$var_name"] = $res;
	}
	echo $_SESSION["recent_$var_name"];
	echo "<form action=\"{$_SERVER['REQUEST_URI']}\" method=\"post\"><input type=\"submit\" name=\"reload_recent_$var_name\" value=\"Reload\"></form>";
}

// Recently viewed entries 
if (isset($CID) && $USER) {
	do_print_recent("time_last_comment", "Recently Commented");
	do_print_recent("time_submit", "Recently Submitted");
	do_print_recent("time_last_update", "Recently Updated");
}
?>

</div>

</div>
</body>
</html>
