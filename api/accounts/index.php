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
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$users = $con->getData("SELECT *, (SELECT offices.shortname FROM offices WHERE offices.id = users.div_id) div_id, (SELECT groups.group_name FROM groups WHERE groups.id = users.group_id) group_name FROM users ORDER BY users.id");	
	
	foreach ($users as $i => $user) {
	
		unset($users[$i]['pw']);
		
	};

    return $response->withJson($users);

});

# add account
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	$data = $request->getParsedBody();
	
	unset($data['id']);
	$con->insertObj($data);

});

# update account
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	$data = $request->getParsedBody();

	$con->updateObj($data,'id');

});

# view account
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";

	$user = $con->get(array("id"=>$args['id']));
	
	$group_id = ($user[0]['group_id'])?$user[0]['group_id']:0;

	$group = $con->getData("SELECT id, group_name FROM groups WHERE id = $group_id");

	$user[0]['group_id'] = ($user[0]['group_id'])?$group[0]:array("id"=>0,"group_name"=>"");



	$div_id = ($user[0]['div_id'])?$user[0]['div_id']:0;

	$office = $con->getData("SELECT id, office FROM offices WHERE id = $div_id");

	$user[0]['div_id'] = ($office)?$office[0]:array("id"=>0,"office"=>"");

    return $response->withJson($user[0]);

});

# delete account
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";
	
	$user = array("id"=>$args['id']);

	$con->deleteData($user);

});

$app->run();

?>