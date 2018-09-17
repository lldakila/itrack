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

# list document types
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$doc_types = $con->getData("SELECT * FROM document_types");	
	
    return $response->withJson($doc_types);

});

# add document type
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";

	$data = $request->getParsedBody();
	
	unset($data['id']);
	$con->insertObj($data);

});

# update document type
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";

	$data = $request->getParsedBody();

	$con->updateObj($data,'id');

});

# view document type
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";

	$doc_type = $con->get(array("id"=>$args['id']));

    return $response->withJson($doc_type[0]);

});


# delete document type
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";
	
	$doc_type = array("id"=>$args['id']);

	$con->deleteData($doc_type);

});

$app->get('/staffs', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";	

	require_once '../../system_setup.php';	
	$system_setup = system_setup;
	$setup = new setup($system_setup);
	
	$in = $setup->get_values_as_string(1);
	
	$staffs = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE group_id IN ($in)");	

    return $response->withJson($staffs);

});

$app->run();

?>