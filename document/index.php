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

$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('views/');
};

$app->get('/view/{id}', function ($request, $response, $args) {
	
	require_once '../path_url.php';
	
	# query
	
	#
	
    return $this->view->render($response, 'document.html', [
		'path'=>$base_path,	
		'url'=>$base_url,
        'id'=>$args['id']
    ]);
	

})->setName('document');

$app->run();

?>