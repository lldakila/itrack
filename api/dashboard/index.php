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
	
	$dashboard = array(
		"opa"=>array(
			"show"=>false,
			"new_documents"=>0,
			"for_initial"=>0,
			"initialed_documents"=>0,
			"for_approval"=>0,
			"approved_documents"=>0
		),
		"opg"=>array(
			"show"=>false,
			"for_initial"=>0,
			"initialed_documents"=>0,
			"for_approval"=>0,
			"approved_documents"=>0	
		),
		"office"=>array(
			"show"=>false,
			"description"=>"",
			"incoming"=>0,
			"outgoing"=>0
		)
	);

	require_once '../../system_setup.php';
	require_once '../../functions.php';

	$opa_office = is_office_admin($office);
	$opg_office = is_office_opg($office);	
	$other_office = !$opa_office && !$opg_office;
	
	require_once 'classes.php';
	$dashboard_counters = new dashboard_counters($con,$filter,$office);
	
	if ($opa_office) {
		
		$dashboard['opa']['show'] = true;
		
	} elseif ($opg_office) {
		
		$dashboard['opg']['show'] = true;
		
	} else { # other_office
		
		$dashboard['office']['show'] = true;
		$office_description = $con->getData("SELECT office FROM offices WHERE id = $office");
		if (count($office_description)) $dashboard['office']['description'] = $office_description[0]['office'];
		$dashboard['office']['incoming'] = $dashboard_counters->incoming();
		$dashboard['office']['outgoing'] = $dashboard_counters->outgoing();
		
	}
	
	// var_dump($dashboard_counters->approved());
	
	// exit();
	
	
/* 	$sql = "SELECT count(*) new_documents FROM documents WHERE ".$filters['documents'][$selected];
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
	};	*/
	
    // return $response->withJson($filter);
    return $response->withJson($dashboard);
	
});

$app->run();

?>