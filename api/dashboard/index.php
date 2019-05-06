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

# dashboard
$app->post('/data', function (Request $request, Response $response, array $args) {

	session_start();

	$office = $_SESSION['office'];
	$user_id = $_SESSION['itrack_user_id'];

	$con = $this->con;
	$con->table = "documents";

	$filter = $request->getParsedBody();

	$selected = $filter['selected']['period'];
	
	$dashboard = array(
		"opa"=>array(
			"new_documents"=>0,
			"for_initial"=>0,
			"initialed_documents"=>0,
			"for_approval"=>0,
			"approved_documents"=>0
		),
		"opg"=>array(
			"for_initial"=>0,
			"initialed_documents"=>0,
			"for_approval"=>0,
			"approved_documents"=>0	
		)
	);
	
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
	
	switch ($selected) {

		case 'date':

			$filters['documents'][$selected] = "document_date LIKE '".date("Y-m-d",strtotime($filter['date']))."%'";
			$filters['tracks'][$selected] = "system_log LIKE '".date("Y-m-d",strtotime($filter['date']))."%'";

		break;

		case 'week':

			$filters['documents'][$selected] = "document_date BETWEEN '".date("Y-m-d",strtotime($filter['week']['from']))."' AND '".date("Y-m-d",strtotime($filter['week']['to']))."'";
			$filters['tracks'][$selected] = "system_log BETWEEN '".date("Y-m-d",strtotime($filter['week']['from']))."' AND '".date("Y-m-d",strtotime($filter['week']['to']))."'";

		break;

		case 'month':
		
			$filters['documents'][$selected] = "document_date LIKE '".$filter['year']."-".$filter['month']['month']."-%'";
			$filters['tracks'][$selected] = "system_log LIKE '".$filter['year']."-".$filter['month']['month']."-%'";
		
		break;
		
		case 'year':
		
			$filters['documents'][$selected] = "document_date LIKE '".$filter['year']."-%'";
			$filters['tracks'][$selected] = "system_log LIKE '".$filter['year']."-%'";
		
		break;
		
	};	
	
	$sql = "SELECT count(*) new_documents FROM documents WHERE ".$filters['documents'][$selected];
	$new_documents = $con->getData($sql);
	
	#
	if (count($new_documents)) {
		$dashboard['opa']['new_documents'] = $new_documents[0]['new_documents'];
	};

	$sql = "SELECT * FROM tracks WHERE ".$filters['tracks'][$selected];
	$initialed_documents = $con->getData($sql);
	
	#
	if (count($initialed_documents)) {
		// $dashboard['new_documents'] = $new_documents[0]['new_documents'];
	};	
	
    // return $response->withJson($sql);
    // return $response->withJson($filter);
    return $response->withJson($dashboard);
	
});

$app->run();

?>