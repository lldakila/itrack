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

# list offices
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$offices = $con->getData("SELECT *,(SELECT departments.dept FROM departments WHERE departments.id = offices.dept_id) dept FROM offices");	
	
    return $response->withJson($offices);

});

# add office
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "offices";

	$data = $request->getParsedBody();
	
	unset($data['id']);
	$con->insertObj($data);

});

# update office
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "offices";

	$data = $request->getParsedBody();

	$con->updateObj($data,'id');

});

# view office
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "offices";

	$office = $con->get(array("id"=>$args['id']));

	if ($office[0]['dept_id']==null) $office[0]['dept_id'] = 0;

	$dept_id = $con->getData("SELECT id, dept, shortname FROM departments WHERE id = ".$office[0]['dept_id']);

	$office[0]['dept_id'] = (count($dept_id))?$dept_id[0]:array("id"=>0,"dept"=>"","shortname"=>"");

    return $response->withJson($office[0]);

});


# delete office
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "offices";
	
	$office = array("id"=>$args['id']);

	$con->deleteData($office);

});

$app->run();

?>