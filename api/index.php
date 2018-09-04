<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once '../db.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$container = new \Slim\Container;
$app = new \Slim\App($container);

$container = $app->getContainer();
$container['con'] = function ($container) {
	$con = new pdo_db();
	return $con;
};

# list groups
$app->get('/groups/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$groups = $con->getData("SELECT * FROM groups");	

    return $response->withJson($groups);

});

# add group
$app->post('/groups/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";

	require_once '../classes.php';
	
	$data = $request->getParsedBody();
	
	$privileges = [];
	if (isset($data['privileges'])) {
		
		$arrayHex = new ArrayHex();
			
		$privileges = $arrayHex->toHex(json_encode($data['privileges']));
		$data['group']['privileges'] = $privileges;
		
	};
	
	unset($data['group']['id']);
	$con->insertData($data['group']);

});

# add group
$app->put('/groups/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";

	require_once '../classes.php';

	$data = $request->getParsedBody();

	$privileges = [];
	if (isset($data['privileges'])) {

		$arrayHex = new ArrayHex();

		$privileges = $arrayHex->toHex(json_encode($data['privileges']));
		$data['group']['privileges'] = $privileges;

	};

	$con->updateData($data['group'],'id');

});

$app->run();

?>