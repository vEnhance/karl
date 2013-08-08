<?
$TITLE_HEAD = "Problem: ";
$TITLE_TAIL = "";
require './internal/common.php';
require './internal/header.php';

$ID = mysql_real_escape_string($_GET['id']);
if (!$ID) {
	die("NO YOU");
}

$contest_name = $_SESSION['OK_contests'][$CID];

$submit_day = $PROB_ROW['time_submit'];
$update_day = $PROB_ROW['time_last_update'];

?>

<?
include './internal/buttons.php';
?>

<!-- Actual problem -->
<div class="entry">
	<h1>
		<a href="view_set.php?cid=<? echo $CID; ?>"><? echo $PROB_ROW['zindex']; ?>.</a> 
		<a href="view_set.php?cid=<? echo $CID; ?>&difficulty=<? echo $DIFFICULTY; ?>"><? echo $PROB_ROW['title']; ?></a>
	</h1>

	<h2>by <strong><? echo $PROB_ROW['author']; ?></strong>, <? echo date('F j, Y, g:i:s a', $submit_day); ?></h2>
	<div class="entrywrap">
		<div class="message"><span class="math_content"><? echo $PROB_ROW['statement']; ?></span></div>
		<div class="efooter">
			<div class="actions">
			<ul>
			<li><a class="post-replies" href="propose_problem.php?id=<? echo $ID; ?>">Edit</a></li>
			<li><a class="post-replies" href="ch_vote.php?id=<? echo $ID; ?>&delta=1">N=<? echo $PROB_ROW['votes']; ?></a></li>
			</ul>
			</div>
			<span class="addthis_button" style="text-align: right;">
				<span class="prob_hashtag">#(<? echo $PROB_ROW['subject']; ?>),
				<a style="color:inherit;" href="view_set.php?cid=<? echo $CID; ?>&difficulty=<? echo $DIFFICULTY; ?>">
					d=<? echo $DIFFICULTY; ?>
				</a></span>
			<br>Last Updated: <? echo date('F j, Y, g:i:s a', $update_day); ?></span>
		</div>
	</div>
</div>

<?
// if $_GET['success'] be happy.
if ($_GET['success'] == 1) {
	echo '<div class="success">Problem succesfully updated!</div>';
}
?>

<h3>
<?
if ($PROB_ROW['answer'] !== '') echo ' <a href="javascript:toggleHide(\'hide_answer\');" class="hider">Show Answer</a> ';
else echo ' No Answer ';
?>
&bull;
<?
if ($PROB_ROW['solution']) echo ' <a href="javascript:toggleHide(\'hide_solution\');" class="hider">Show Solution</a> ';
else echo ' No Solution ';
?>
&bull;
<a class="post-replies" href="/ch_vote.php?id=<? echo $ID; ?>&delta=1">+1</a>
&bull;
<a class="post-replies" href="/ch_vote.php?id=<? echo $ID; ?>&delta=-1">-1</a>
</h3>

<div id="hide_answer" class="hidden"><div class="info"><b>Answer:</b> <? echo $PROB_ROW['answer']; ?></div></div>
<div id="hide_solution" class="hidden"><div class="info"><span class="math_content"><? echo $PROB_ROW['solution']; ?></span></div></div>

<form action="propose_problem.php" method="post">
<input type="hidden" name="id" value="<? echo $ID; ?>">
<input type="hidden" name="is_comment" value="1">
<h1>Comments</h1>
	<? do_print_textarea("comments", "Comments A"); do_print_submit("submit", "Update"); ?>
	<br>
	<a href="javascript:toggleHide('hide_comments_b');" class="hider">Show Comments B (<? echo line_count($PROB_ROW['comments_spoiler']); ?> lines)</a><br><br>
	<div id="hide_comments_b" class="hidden">
	<? do_print_textarea("comments_spoiler", "Comments B", "512px"); do_print_submit("submit", "Update"); ?>
</div>
</form>

<? 
require './internal/footer.php';
?>
