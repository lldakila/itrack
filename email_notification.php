<?php

header("content-type: application/json; charset=utf-8");
header("access-control-allow-origin: *");

$url = "https:/itrack.launion.gov.ph/";

$user = $_POST['user'];
$message = $_POST['message'];

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Document Tracking System</title>
  </head>
  <body>
    <header>
		<p>Dear <?php echo $user['fname']." ".$user['lname']; ?>,</p>
	</header>
	<main style="margin-bottom: 50px;">
		<p>You document's recent status is <strong><?=$message?></strong></p>
	</main>
	<footer>
		<p>Regards,</p>
		<img src="<?=$url?>images/logo/itrack-logo-large.png" alt="Logo" title="Logo" style="width: 198px; height: 43px;" width="198" height="48">
		<p><strong>Administrator</strong></p>
	</footer>
</html>