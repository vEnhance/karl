<?
function do_print_textarea($var_name, $meta_name, $height="128px", $default_value=false) {
	if ($default_value === false) {
		global $FILL_IN;
		$default_value = $FILL_IN[$var_name];
	}
	if ($_SESSION['userdata']['use_ace']) {
		$ace_hidden = "unhidden";
		$text_hidden = "hidden";
	}
	else {
		$ace_hidden = "hidden";
		$text_hidden = "unhidden";
	}
	$ace_theme = $_SESSION['userdata']['ace_theme'];
	echo "<a class=\"form_input_name\" href=\"javascript:toggleHide('text_$var_name');toggleHide('ace_$var_name');\">$meta_name</a>
		<a class=\"hider\" href=\"javascript:updatePreview('$var_name');\">(preview)</a>
		<div id=\"preview_$var_name\" class=\"hidden\">
			<div class=\"info\">
			<b>Preview:</b>
			<span id=\"preview_content_$var_name\" class=\"math_content\"></span>
			</div>
		</div>
		<textarea id=\"text_$var_name\" name=\"$var_name\" class=\"$text_hidden\" style=\"height:$height; width:90%;\">$default_value</textarea>
		<span class=\"$ace_hidden\" id=\"ace_$var_name\">
		<div id=\"editor_$var_name\" style=\"width: 64\%; position:relative; height: $height; font-size:11pt;\"></div>
		</span>
		";
	echo "<script>
		var editor_$var_name = ace.edit(\"editor_$var_name\");
		var textarea_$var_name = \$('textarea[name=\"$var_name\"]');
		editor_$var_name.setTheme(\"ace/theme/$ace_theme\");
		editor_$var_name.getSession().setMode(\"ace/mode/latex\");
		editor_$var_name.getSession().setUseWrapMode(true);
		editor_$var_name.setShowPrintMargin(false);
		editor_$var_name.getSession().setValue(textarea_$var_name.val());
		editor_$var_name.getSession().on('change', function() { textarea_$var_name.val(editor_$var_name.getSession().getValue()); });
		\$('textarea[name=\"$var_name\"]').change(function() { ace.edit('editor_$var_name').getSession().setValue(textarea_$var_name.val()); });
		</script>
	";
	echo "<br />" . "\n";
}

function do_print_text($var_name, $meta_name, $default_value=false, $size=20, $input_type="text") {
	if ($default_value === false) {
		global $FILL_IN;
		$default_value = $FILL_IN[$var_name];
	}
	echo "<td class=\"form_input_name\">$meta_name</td>";
	echo "<td><input name=\"$var_name\" class=\"text\" type=\"$input_type\" value=\"$default_value\" size=\"$size\"></td>";
}


function do_print_submit($var_name="submit", $meta_name="Submit") {
	echo "<input class=\"inputbutton\" name=\"$var_name\" value=\"$meta_name\" type=\"submit\">";
	echo "<br />";
}

function do_print_selector_minimal($var_name, $selected) {
	foreach ($_SESSION['OK_contests'] as $this_cid => $this_row) {
		$this_name = $this_row['name'];
		if ($this_cid==$selected) {
			echo "<option value=\"$this_cid\" selected=\"selected\">" . $this_name . "</option>" . "\n";
		}
		else {
			echo "<option value=\"$this_cid\">" . $this_name . "</option>" . "\n";
		}
	}
}

function do_print_selector($var_name="cid", $selected=false) {
	echo "<select name=\"$var_name\">" . "\n";
	do_print_selector_minimal($var_name, $selected);
	echo "</select>" . "\n";
}
	


function line_count($s) {
	$ans = substr_count($s, "\r\n");
	if ($s != "") { $ans += 1; }
	return $ans;
}



// Try to guess title
if ($ID) {
	$TITLE = $TITLE_HEAD . $PROB_ROW['topic'] . $TITLE_TAIL;
}
else if ($CID) {
	$TITLE = $TITLE_HEAD . $OK_CONTESTS[$CID]['name'] . $TITLE_TAIL;
}
else {
	$TITLE = $TITLE_HEAD . $TITLE_TAIL;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta charset="UTF-8">
<meta name="Keywords" content="mop nimo omo math karl cms">
<meta name="Description" content="Contest management system for math contests" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<link href="/css/hyperion.css" type="text/css" rel="stylesheet" />
<?
// figure out css numbers.
$css_number = $USER_DATA['css'];
if (!$css_number) { $css_number = 53544; }
$url_array = array();
$aops_head = "http://www.aops.com/Forum/blog/styles/hyperion/styles/";
$url_array["b"] = $aops_head . "$css_number.css";
$url_array["c"] = $aops_head . "c$css_number.css";
$url_array["z"] = "";
$url_array["k"] = "";
$array_css_use_default_list = array('b', 'c', 'z');

foreach ($url_array as $prefix => $remote_location) {
	if (file_exists($WEB_ROOT . "css/$prefix$css_number.css"))
		echo "<link href=\"/css/$prefix$css_number.css\" type=\"text/css\" rel=\"stylesheet\" />";
	else if ($remote_location != "")
		echo "<link href=\"$remote_location\" type=\"text/css\" rel=\"stylesheet\" />";
	else if (in_array($prefix, $array_css_use_default_list))
		echo "<link href=\"/css/{$prefix}53544.css\" type=\"text/css\" rel=\"stylesheet\" />";
	echo "\n";
}
?>
<script type="text/x-mathjax-config">
MathJax.Hub.Config({tex2jax: 
	{
		inlineMath: [['$','$'], ['\\(','\\)']],
		displayMath: [['\\[', '\\]']],
	}
});
</script>
<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script src="http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>


<script type="text/javascript">
function toggleHide(divID) {
	var item = document.getElementById(divID);
	if (item) {
	item.className=(item.className=='hidden')?'unhidden':'hidden';
	}
}
function updatePreview(varname) {
	$('#preview_content_' + varname).html($('#text_' + varname).val());
	$('#preview_' + varname).removeClass('hidden');
	MathJax.Hub.Queue(['Typeset',MathJax.Hub,'preview_content_' + varname]);
}
// Taken from w3schools
function getCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++) {
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		  x=x.replace(/^\s+|\s+$/g,"");
		  if (x==c_name) { return unescape(y); }
	}
	return 0;
}
function setCookie(c_name,value,exdays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
</script>

<title>KARL &bull; <? echo $TITLE; ?></title>
</head>

<body>
<div id="navigation_box">
	<div id="left_navigation_box">
	&copy; 2013 Evan Chen
	</div>
</div>

<div id="header">
	<h1>Korean Amateur RTS League</h1>
</div>

<div id="content">
<div id="main">
