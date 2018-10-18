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

# list documents
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "documents";
	
	require_once '../../document-info.php';
	
	$documents = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type FROM documents");	

	foreach ($documents as $i => $document) {
		
		$documents[$i] = document_info_complete($con,$document);
		
	};
	
    return $response->withJson($documents);

});

# delete document
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "documents";
	
	$document = array("id"=>$args['id']);

	$files = $con->getData("SELECT file_name FROM files WHERE document_id = ".$args['id']);	
	
	$files_dir = "../../files/";
	foreach ($files as $file) {
		
		if (file_exists($files_dir.$file['file_name'])) unlink($files_dir.$file['file_name']);
		
	};	
	
	$con->deleteData($document);

});

$app->run();

?>