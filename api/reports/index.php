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

	require_once '../../functions.php';
	require_once '../../document-info.php';
	require_once '../../system_setup.php';
	require_once '../../tracks.php';

	$system_setup = system_setup;
	$setup = new setup($system_setup);	

	$con = $this->con;
	
	$data = $request->getParsedBody();
	$metas = $data['meta'];
	$period = $data['period'];
	
	$selected = $period['selected']['period'];
	
	$filters = array(
		"documents"=>array(
			"date"=>"",
			"week"=>"",
			"month"=>"",
			"year"=>""
		),
		"tracks"=>array(
			"date"=>"",
			"week"=>"",
			"month"=>"",
			"year"=>""
		),		
	);
	
	$coverage = "";
	
	switch ($selected) {

		case 'date':

			$filters['documents'][$selected] = "document_date LIKE '".date("Y-m-d",strtotime($period['date']))."%'";
			$filters['tracks'][$selected] = "system_log LIKE '".date("Y-m-d",strtotime($period['date']))."%'";
			
			$coverage = date("M j, Y",strtotime($period['date']));

		break;

		case 'week':

			$filters['documents'][$selected] = "document_date BETWEEN '".date("Y-m-d",strtotime($period['week']['from']))."' AND '".date("Y-m-d",strtotime($period['week']['to']))."'";
			$filters['tracks'][$selected] = "system_log BETWEEN '".date("Y-m-d",strtotime($period['week']['from']))."' AND '".date("Y-m-d",strtotime($period['week']['to']))."'";
			
			$coverage = date("M j - ",strtotime($period['week']['from'])).date("M j, Y",strtotime($period['week']['to']));

		break;

		case 'month':
		
			$filters['documents'][$selected] = "document_date LIKE '".$period['year']."-".$period['month']['month']."-%'";
			$filters['tracks'][$selected] = "system_log LIKE '".$period['year']."-".$period['month']['month']."-%'";
			
			$coverage = $period['month']['text'].", ".$period['year'];
		
		break;
		
		case 'year':
		
			$filters['documents'][$selected] = "document_date LIKE '".$period['year']."-%'";
			$filters['tracks'][$selected] = "system_log LIKE '".$period['year']."-%'";
		
			$coverage = $period['year'];
		
		break;
		
	};
	
	# meta
	$meta_filters = "";
	
	$meta_fields = array("origin","communication","document_transaction_type","doc_type");
	
	foreach ($metas as $key => $meta) {
		
		if ($meta['id']==0) continue;
		
		if (in_array($key,$meta_fields)) {
			
			$meta_filters .= " AND $key = ".$meta['id'];
			
		};
		
	};

	$sql = "SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date FROM documents WHERE ".$filters['documents'][$selected].$meta_filters;
	$documents = $con->getData($sql);	
	
	foreach ($documents as $i => $document) {
		
		$documents[$i] = document_info_reports($con,$document);
		
		$documents[$i]['date'] = date("M j, Y",strtotime($document['document_date']));
		
	};

	$report = [
		array(
			"coverage"=>$coverage,
			"origin"=>$metas['origin']['shortname'],
			"doc_type"=>$metas['doc_type']['document_type'],
			"communication"=>$metas['communication']['communication'],
			"document_transaction_type"=>$metas['document_transaction_type']['transaction'],
			"action"=>"",
			"rows"=>$documents
		)
	];
	
	return $response->withJson($report);

});

$app->run();

?>