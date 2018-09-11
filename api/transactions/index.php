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

# list transactions
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$trans = $con->getData("SELECT * FROM transactions");	
	
    return $response->withJson($trans);

});

# add transaction
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "transactions";

	$data = $request->getParsedBody();
	
	unset($data['id']);
	$con->insertObj($data);

});

# update transaction
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "transactions";

	$data = $request->getParsedBody();

	$con->updateObj($data,'id');

});

# view transaction
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "transactions";

	$tran = $con->get(array("id"=>$args['id']));

    return $response->withJson($tran[0]);

});


# delete transaction
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "transactions";
	
	$tran = array("id"=>$args['id']);

	$con->deleteData($tran);

});

$app->run();

?>