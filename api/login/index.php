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
	
	$data = array("id"=>$id);
	$url = "http://".$_SERVER['HTTP_HOST']."/email_password.php";

	$options = array(
		'http'=>array(
			'header'=>"Content-type: application/x-www-form-urlencoded\r\n",
			'method'=>'POST',
			'content'=>http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$message = file_get_contents($url, false, $context);	
	
	$address = "sly@christian.com.ph";
	$subject = "Password Recovery";

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: iTrack (Document Tracking System) <sly14flores@gmail.com>' . "\r\n";

	$send = mail($address,$subject,$message,$headers);
	
	if (!$send) {
		
		$respond = array("status"=>false);
		
	} else {
		
		$respond = array("status"=>true);
		
	};
	
	return $response->withJson($respond);

});

$app->run();

?>