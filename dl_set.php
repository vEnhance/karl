<?

$TITLE_HEAD = "Download ";
$TITLE_TAIL = "";
require './internal/common.php';

if (isset($_REQUEST['submit']) || !isset($_REQUEST['ask'])) {
	header("Content-Type: text/plain");

	$menu = array('author' => 1, 'answer' => 1, 'topic' => 0, 'difficulty' => 0, 'votes' => 0);
	if (isset($_REQUEST['submit'])) {
		foreach ($menu as $entree => $default) {
			if (isset($_REQUEST['include_' . $entree])) {
				$menu[$entree] = 1;
			}
			else {
				$menu[$entree] = 0;
			}
		}
	}
	$ordered_soln = isset($_REQUEST['include_solution']);
	$ordered_preamble = isset($_REQUEST['include_preamble']); // default value

	$query = "SELECT * FROM Problems WHERE cid=$CID ORDER BY zindex";
	$res = mysql_query($query);
	$contest_name = $OK_CONTESTS[$CID]['name'];

	if ($ordered_preamble) {
		echo '\documentclass[11pt]{article}' . "\n";
		echo '\usepackage{amsmath,amsthm,amssymb}' . "\n";
		echo '\usepackage[margin=1in]{geometry}' . "\n";

		foreach ($menu as $entree => $ordered) {
			if ($ordered) {
				echo "\\newcommand{\\set$entree}[1]{}\n";
			}
		}
		if ($ordered_soln) {
			echo "\\newenvironment{soln}{\\begin{proof}[Solution]}{\\end{proof}}\n";
		}
		echo "\n";

		echo '\begin{document}' . "\n";
		echo '\title{' . $contest_name . '}' . "\n";
		echo '\author{KARL}' . "\n";
		echo '\date{\today}' . "\n";
		echo '\maketitle' . "\n";

		echo "\n";
		echo '\begin{enumerate}' . "\n";
		echo "\n";
	}

	while ($prob_row = mysql_fetch_array($res)) {
		// echo "% d={$prob_row['difficulty']}, {$prob_row['votes']} votes\n";
		echo '\item ';
		echo str_replace("\r\n\r\n", "\r\n\t\\par ", $prob_row['statement']);
		echo "\n";
		if ($ordered_soln) {
			echo "\\begin{soln}" . "\n";
			echo $prob_row['solution'];
			echo "\n" . "\\end{soln}";
			echo "\n";
		}
		foreach ($menu as $entree => $ordered) {
			if ($ordered) {
				echo "\t\\set$entree{" . $prob_row[$entree] . "}\n";
			}
		}
		echo "\n";
	}	
	if ($ordered_preamble) {
		echo '\end{enumerate}' . "\n" . '\end{document}';
	}

	exit;
}


require './internal/header.php';

?>

<?
include './internal/buttons.php';
?>

<div class="entry">
	<h1>Entre&eacute;s</h1>
	<h2><? echo $ALL_CONTESTS['cid']['name']; ?></h2>
	<div class="entrywrap">
		<div class="info">Please select which items you would like to purchase from the menu below.</div>
		<form name="download" action="dl_set.php" method="post">
			<input name="cid" type="hidden" value="<? echo $CID; ?>">
			<input name="include_author" type="checkbox" value="1"> Problem author <br>
			<input name="include_answer" type="checkbox" value="1"> Numerical answer <br>
			<input name="include_difficulty" type="checkbox" value="1"> Difficulty <br>
			<input name="include_topic" type="checkbox" value="1"> Problem names <br>
			<input name="include_votes" type="checkbox" value="1"> Votes <br>
			<input name="include_solution" type="checkbox" value="1"> Solution <br>
			<input name="include_solution" type="checkbox" value="1"> TeX Preamble <br>
			<input name="submit" type="submit" value="Order">
		</form>
	</div>
</div>


<?
require './internal/footer.php';
?>

