<? require 'check.php'; ?>
Right now only capability is add_user.php.
<ul>
<?php
if ($handle = opendir('.')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo "<li><a href=\"$entry\">$entry</a></li>";
        }
    }
    closedir($handle);
}
?>
</ul>
