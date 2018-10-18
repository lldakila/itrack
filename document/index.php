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

    return $this->view->render($response, 'document.html', [
		'path'=>$base_path,	
		'url'=>$base_url,
        'id'=>$args['id']
    ]);	

})->setName('document');

$app->get('/view/info/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";	

	require_once '../document-info.php';
	
	$id = $args['id'];
	
	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params FROM documents WHERE id = $id");
	
	if (count($document)) {
		
		# first track
		$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = ".$document['0']['id']."  ORDER BY system_log");
		
		$document[0]['for_initial'] = false;
		$document[0]['for_signature'] = false;
		$document[0]['for_routing'] = false;

		$document[0]['document_dt_add_params'] = json_decode($document[0]['dt_add_params'],false);
		$document[0]['document_action_add_params'] = [];
		
		if (count($tracks)) {
			
			$first_track = $tracks[0];
			
			if ($first_track['track_action']==1) $document[0]['for_initial'] = true;
			if ($first_track['track_action']==2) $document[0]['for_signature'] = true;
			if ($first_track['track_action']==3) $document[0]['for_routing'] = true;
			
			$document[0]['document_action_add_params'] = json_decode($first_track['track_action_add_params'],false);
			
		};
		
		$document[0]['document_date_barcode'] = date("M j, Y h:i:s A",strtotime($document[0]['document_date']));
		$document = document_info_complete($con,$document[0]);
		
	};
	
	return $response->withJson($document);

});

$app->get('/for/initial/{id}', function ($request, $response, $args) {

	require_once '../path_url.php';

    return $this->view->render($response, 'initial.html', [
		'path'=>$base_path,	
		'url'=>"../".$base_url,
        'id'=>$args['id']
    ]);
	

})->setName('document');

$app->run();

?>