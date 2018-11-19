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
	
	require_once '../../functions.php';
	require_once '../../document-info.php';
	require_once '../../system_setup.php';	
	require_once '../../tracks.php';
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);		
	
	$documents = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date FROM documents");	

	foreach ($documents as $i => $document) {
		
		$tracks = tracks($con,$setup,$document['id'],$document);
		
		$documents[$i] = document_info_complete($con,$document);
		$documents[$i]['recent_status'] = $tracks[0]['list'][0]['status'];
		
	};
	
    return $response->withJson($documents);

});

# barcode id
$app->get('/barcode/{barcode}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "documents";	

	$barcode = $args['barcode'];
	
	$status = false;
	$document_id = null;
	$document = $con->getData("SELECT id FROM documents WHERE barcode = '$barcode'");
	
	if (count($document)) {
		$status = true;
		$document_id = $document[0]['id'];
	}
	
    return $response->withJson(array("id"=>$document_id,"status"=>$status));

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