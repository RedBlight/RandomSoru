<?php

  	// Input code is taken as-is for testing purposes only. It should be secured in production environments.
	$code = $_POST['code'];
	if (get_magic_quotes_gpc()) $code = stripslashes($code);
?>
<form method="post">
	<textarea name="code" cols="50" rows="10"><?php if ($code) echo $code; else echo '$v = rand(1,10);'; ?></textarea><br />
	<input type="submit" value="safer eval()" />
	<input type="hidden" name="eval" value="1" />
</form>
<?php

if ($_POST['eval']) { // Only if the form has been submitted

	// Files required by safer eval()
	include_once "config.safereval.php";
	include_once "class.safereval.php";

	// Functions for benchmarking
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;

	// Assigning default values for test variables
	$v = 0;
	$x = 0;

	// These are the actualy lines needed	
	$se = new SaferEval();
	$errors = $se->checkScript($code, 1);

	// Functions for benchmarking
	$mtime = microtime ();
	$mtime = explode (' ', $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$endtime = $mtime;
	$totaltime = round (($endtime - $starttime), 6);

?>

<p>The value of the <strong>$v</strong> variable (allowed) is: <strong><?php echo htmlentities($v); ?></strong></p>
<p>The value of the <strong>$x</strong> variable (disallowed) is: <strong><?php echo htmlentities($x); ?></strong></p>
<p><em>Secure eval()</em> took: <strong><?php echo $totaltime; ?></strong> seconds.</p>

<?php

	// Error output
	if ($errors) print_r($se->htmlErrors($errors));
	else echo '<p>No errors.</p>';
}

?>