<?
$NEEDS_LOGIN = 0;
require '../internal/common.php';
if ($_SESSION['userdata']['groups'] != 'admin') {
	// Pretend to 404 I guess
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
<hr>
<address>Apache/2.2.14 (Ubuntu) Server on Port 80</address>
<!--
 We're no strangers to love
You know the rules and so do I
A full commitment's what I'm thinking of
You wouldn't get this from any other guy

I just wanna tell you how I'm feeling
Gotta make you understand

CHORUS:
Never gonna give you up, never gonna let you down
Never gonna run around and desert you
Never gonna make you cry, never gonna say goodbye
Never gonna tell a lie and hurt you

We've known each other for so long
Your heart's been aching but you're too shy to say it
Inside we both know what's been going on
We know the game and we're gonna play it

And if you ask me how I'm feeling
Don't tell me you're too blind to see

CHORUS
CHORUS

(Ooh give you up)
(Ooh give you up)
(Ooh) never gonna give, never gonna give (give you up)
(Ooh) never gonna give, never gonna give (give you up)

We've known each other for so long
Your heart's been aching but you're too shy to say it
Inside we both know what's been going on
We know the game and we're gonna play it

I just wanna tell you how I'm feeling
Gotta make you understand

CHORUS 
-->
</body></html>
<?
	die();
}
?>
