<?
require './internal/common.php';
header("Content-Type: text/plain");

$query = "SELECT * FROM Problems WHERE cid=$CID ORDER BY zindex";
$res = mysql_query($query);
$contest_name = $OK_CONTESTS[$CID]['name'];

while ($prob_row = mysql_fetch_array($res)) {
	echo yaml_emit($prob_row);
}	

?>
