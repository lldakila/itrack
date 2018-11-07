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

$app->get('/info', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";
	
	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];	
	
	$user = $con->getData("SELECT uname FROM users WHERE id = $session_user_id");	

    return $response->withJson($user[0]);

});

$app->get('/security', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";
	
	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];	
	
	$user = $con->getData("SELECT pw FROM users WHERE id = $session_user_id");	

    return $response->withJson($user[0]);

});

$app->post('/username', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];	

	$data = $request->getParsedBody();
	if (!isset($data['uname'])) $data['uname'] = "";

	$user = $con->getData("SELECT * FROM users WHERE id != $session_user_id AND uname = '".$data['uname']."'");	

	$res = array("status"=>false);	
	if (count($user)) $res = array("status"=>true);

    return $response->withJson($res);

});

$app->post('/update/info', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];		
	
	$data = $request->getParsedBody();

	$con->updateData(array("id"=>$session_user_id,"uname"=>$data['uname']),'id');

});

$app->post('/update/security', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];		
	
	$data = $request->getParsedBody();

	$con->updateData(array("id"=>$session_user_id,"pw"=>$data['pw']),'id');

});

$app->run();

?>