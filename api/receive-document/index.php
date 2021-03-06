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

$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('views/');
};

# communications
$app->get('/communications', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "communications";
	
	$communications = $con->all(['id','communication','shortname']);
	
    return $response->withJson($communications);

});

# transactions
$app->get('/transactions', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "transactions";
	
	$transactions = $con->all(['id','transaction','days']);
	
    return $response->withJson($transactions);

});

# offices
$app->get('/offices', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "offices";

	$offices = $con->all(['id','office','shortname']);

	/* $con->table = "users";
	foreach ($offices as $i => $office) {

		$offices[$i]['staffs'] = $con->get(["div_id"=>$office['id']],["id","CONCAT(fname, ' ', lname) fullname"]);

	}; */

    return $response->withJson($offices);

});

# doctype
$app->get('/doctype', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";
	
	$doc_types = $con->all(["id","document_type","shortname","transaction_id"]);
	
    return $response->withJson($doc_types);

});

# actions
$app->get('/actions', function (Request $request, Response $response, array $args) {

	session_start();

	$con = $this->con;

	require_once '../../document-actions.php';
	require_once '../../actions-params.php';

	$document_actions = document_actions;
	
	foreach ($document_actions as $da) {

		$actions[$da['key']] = array(
			"description"=>$da['description'],		
			"params"=>get_params($actions_params,$da['id']),
			"value"=>false
		);
	
	};

    return $response->withJson($actions);

});

# document type additional parameters
$app->get('/dt_add_params/{id}', function (Request $request, Response $response, array $args) {

	$id = $args['id'];
	
	require_once '../../dt-additional-params.php';
	
	// $dt_add_params = get_params(dt_add_params,$id);
	
    // return $response->withJson($dt_add_params);
    return $response->withJson([]);

});

# action additional parameters
$app->get('/action_params/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;

	$id = $args['id'];
	
	// require_once '../../document-actions.php';
	require_once '../../actions-params.php';
	
	$action_params = get_params($actions_params,$id);
	
    return $response->withJson($action_params);

});

# add document
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "documents";

	$data = $request->getParsedBody();

	require_once '../../handlers/folder-files.php';
	require_once '../../document-info.php';	
	require_once '../../system_setup.php';
	require_once 'classes.php';
	require_once '../../functions.php';
	require_once '../../notify.php';

	$system_setup = system_setup;
	$setup = new setup($system_setup);

	session_start();

	# for barcode
	$to_barcode = array(
		"origin"=>$data['origin']['id'],
		"office"=>$data['origin']['shortname'],
		"doctype"=>$data['doc_type']['id'],
		"doctype_shortname"=>$data['doc_type']['shortname']
	);
	#

	# document_dt_add_params
	$document_dt_add_params = $data['document_dt_add_params'];
	unset($data['document_dt_add_params']);
	#	
	
	# actions
	$actions = $data['actions'];
	unset($data['actions']);
	#

	$data['user_id'] = $_SESSION['itrack_user_id'];
	$data['origin'] = $data['origin']['id'];
	$data['doc_type'] = $data['doc_type']['id'];
	$data['communication'] = $data['communication']['id'];
	$data['document_transaction_type'] = $data['document_transaction_type']['id'];	
	
	$data['is_rush'] = ($data['is_rush'])?1:0;
	
	$uploads = array("files"=>$data['files']);	
	unset($data['files']);

	$data['barcode'] = barcode($con,$to_barcode)['barcode'];
	$data['doctype_series'] = barcode($con,$to_barcode)['series'];

	$data['dt_add_params'] = json_encode($document_dt_add_params);
	
	unset($data['id']);

	$con->insertData($data);

	$id = $con->insertId;
	
	# notify Liaisons AOs AAsts AAs in originating office
	$initial_office = $setup->get_setup_as_string(4);	
	$all = $setup->get_setup_as_string(10);
	notify($con,"added",array("doc_id"=>$id,"header"=>$data['doc_name'],"group"=>$all,"office"=>$data['origin'],"initial_office"=>$initial_office,"recipient"=>$_SESSION['itrack_user_id']));	

	# notify admin staffs
	$admin_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$admin_staffs);

	foreach ($notify_pa_staffs as $nps) {
	
		notify($con,"added",array("notify_user"=>$nps['id'],"doc_id"=>$id,"header"=>$data['doc_name'],"group"=>0,"office"=>$data['origin'],"initial_office"=>$initial_office,"recipient"=>$_SESSION['itrack_user_id']),false);
	
	};

	# tracks
	$con->table = "tracks";
	
	foreach ($actions as $action) {				
		
		if ($action['value']) {						

			$track_action = $action['params'][0]['action_id'];

			# transit
			$transit = array(
				"id"=>1,
				"picked_up_by"=>null,
				"received_by"=>null,
				"office"=>$setup->get_setup_as_string(4),
				"released_to"=>null,
				"filed"=>false,
			);

			$track = array(
				"document_id"=>$id,
				"office_id"=>$_SESSION['office'],
				"track_action"=>$track_action,
				"track_action_add_params"=>json_encode($action['params'][0]),
				"track_action_status"=>null,
				"track_user"=>$_SESSION['itrack_user_id'],
				"transit"=>json_encode($transit)
			);

			$con->insertData($track);

		};		
	
	};
	#
	
	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date FROM documents WHERE id = $id");
	if (count($document)) {
		
		$staff = $con->getData("SELECT CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$document[0]['user_id']);
		$document[0]['receiver'] = $staff[0]['fullname'];
		unset($document[0]['user_id']);
		
		$document[0]['document_date_barcode'] = date("M j, Y h:i:s A",strtotime($document[0]['document_date']));
		$document[0]['date'] = date("M j, Y",strtotime($document[0]['document_date']));
		$document[0]['time'] = date("h:i:s A",strtotime($document[0]['document_date']));

		$document = document_info($con,$document[0]);
		
	};
	
	uploadFiles($con,$uploads,$data['barcode'],$id,"../../files",true);

	return $response->withJson($document);

});

$app->run();

?>