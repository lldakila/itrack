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
$app->post('/documents', function (Request $request, Response $response, array $args) {

	// $con = $this->con;
	
	$report = [
		array(
			"msg1"=>"Hello World!",
			"msg2"=>"Lorem Ipsum",
			"rows"=>array(
				array("id"=>1,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>2,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>3,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>4,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>5,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>6,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>7,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>8,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>9,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>10,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>11,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>12,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>13,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>14,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>15,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>16,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>17,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>18,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>19,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>20,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>21,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>22,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>23,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>24,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>25,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>26,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>27,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>28,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>29,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>30,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>31,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>32,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>33,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>34,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>35,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>36,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>37,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>38,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>39,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>40,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>41,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>42,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>43,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>44,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>45,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>46,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>47,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>48,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>49,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>50,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>51,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>52,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>53,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>54,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>55,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>56,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>57,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>58,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>59,"first"=>"lorem","last"=>"ipsum"),
				array("id"=>60,"first"=>"lorem","last"=>"ipsum")
			)
		)
	];
	
	return $response->withJson($report);

});

$app->run();

?>