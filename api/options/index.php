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

# list referral_options
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$referral_options = $con->getData("SELECT * FROM referral_options");	
	
    return $response->withJson($referral_options);

});
 
# add option
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "referral_options";

	$data = $request->getParsedBody();
	
	unset($data['id']);
	$con->insertObj($data);

});

# update option
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "referral_options";

	$data = $request->getParsedBody();

	$con->updateObj($data,'id');

});

# view option
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "referral_options";

	$option = $con->get(array("id"=>$args['id']));

    return $response->withJson($option[0]);

});


# delete option
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "referral_options";
	
	$option = array("id"=>$args['id']);

	$con->deleteData($option);

});

$app->run();

?>