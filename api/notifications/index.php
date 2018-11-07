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

# list accounts
$app->get('/fetch', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "notifications";
	
	require_once '../../notify.php';

	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];	
	
	$notifications = $con->getData("SELECT * FROM notifications WHERE user_id = $session_user_id AND dismiss = 0");	
	
	foreach ($notifications as $i => $notification) {
	
		$notifications[$i]['ago'] = ago($notification['system_log']);
		
	};

    return $response->withJson($notifications);

});

$app->run();

?>