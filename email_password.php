<?php

header("content-type: application/json; charset=utf-8");
header("access-control-allow-origin: *");

require_once 'db.php';

$con = new pdo_db();

$user = $con->getData("SELECT fname, lname, pw FROM users WHERE id = ".$_POST['id']);
$data = $user[0];

$url = "https:/itrack.launion.gov.ph/";

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Document Tracking System</title>
  </head>
  <body>
    <header>
		<p>Dear <?php echo $data['fname']." ".$data['lname']; ?>,</p>
	</header>
	<main style="margin-bottom: 50px;">
		<p>You password is <span style="font-style: italic;"><?=$data['pw']?></span></p>
	</main>
	<footer>
		<p>Regards,</p>
		<img src="<?=$url?>images/logo/itrack-logo-large.png" alt="Logo" title="Logo" style="width: 198px; height: 43px;" width="198" height="48">
		<p><strong>Administrator</strong></p>
	</footer>
</html>