<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../vendor/autoload.php';
require_once '../../db.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$container = new \Slim\Container;
$app = new \Slim\App($container);

$container = $app->getContainer();
$container['con'] = function ($container) {
	$con = new pdo_db();
	return $con;
};

# check username
$app->post('/username', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	$data = $request->getParsedBody();

	$user = $con->getData("SELECT email_address FROM users WHERE uname = '".$data['username']."'");
	
    return $response->withJson($user[0]);

});

# email
$app->post('/email', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	$data = $request->getParsedBody();

	$user = $con->getData("SELECT id, email_address FROM users WHERE uname = '".$data['username']."'");
	
	$id = $user[0]['id'];
	$email = $user[0]['email_address'];

	$user = $con->getData("SELECT fname, lname, pw FROM users WHERE id = $id");
	
$message = <<<EOD
<!doctype html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<title>Document Tracking System</title>
  </head>
  <body>
	<header>
		<p>Dear {$user[0]['fname']} {$user[0]['lname']},</p>
	</header>
	<main style="margin-bottom: 50px;">
		<p>You password is <span style="font-style: italic; font-weight: bold;">{$user[0]['pw']}</span></p>
	</main>
	<footer>
		<p>Regards,</p>
		<img src="https://itrack.launion.gov.ph/images/logo/itrack.png" alt="Logo" title="Logo" style="width: 198px; height: 43px;" width="198" height="48">
		<p><strong>Administrator</strong></p>
	</footer>
</html>
EOD;
	
	// $address = "sly@christian.com.ph";
	$address = $email;
	$subject = "Password Recovery";

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: iTrack (Document Tracking System) <sly14flores@gmail.com>' . "\r\n";

	require_once '../../phpmailer/email.php';
	
	$send = sendEmail($address,$subject,$message);
	
	return $response->withJson($send);

});

$app->run();

?>