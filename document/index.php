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
		'page'=>'document',
		'path'=>$base_path,
		'url'=>$base_url,
        'id'=>$args['id']
    ]);	

})->setName('document');

$app->get('/view/info/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";	

	require_once '../handlers/folder-files.php';
	require_once '../document-info.php';
	require_once '../functions.php';
	
	$id = $args['id'];
	
	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params FROM documents WHERE id = $id");
	
	if (count($document)) {
		
		# first track
		// $tracks = $con->getData("SELECT * FROM tracks WHERE document_id = ".$document[0]['id']." ORDER BY system_log LIMIT 1");
		
		$document[0]['dt_add_params'] = json_decode($document[0]['dt_add_params'],false);
		$document[0]['document_dt_add_params'] = $document[0]['dt_add_params'];
		
		$document[0]['document_date_barcode'] = date("M j, Y h:i:s A",strtotime($document[0]['document_date']));
		
		$document[0]['files'] = [];
		$document[0]['delete_files'] = [];

		$files = get_files("../files/",$document[0]['barcode']);
		$document[0]['files'] = $files;

		$document = document_info($con,$document[0]);
		
	};
	
	return $response->withJson($document);

});

# actions
$app->get('/view/actions/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;

	require_once '../document-actions.php';
	require_once '../actions-params.php';

	$id = $args['id'];

	$document_actions = document_actions;

	# tracks
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id");

	foreach ($document_actions as $da) {

		$value = false;
		$params = get_params($actions_params,$da['id']);		
	
		foreach ($tracks as $track) {
			
			if ($da['id'] == $track['track_action']) {
				
				$value = true;
				$params = array(json_decode($track['track_action_add_params'],false));
				
			};
			
		};

		$actions[$da['key']] = array(
			"description"=>$da['description'],
			"params"=>$params,
			"value"=>$value
		);

	};

    return $response->withJson($actions);

});

$app->put('/update/{id}', function ($request, $response, $args) {
	
	$con = $this->con;
	$con->table = "documents";

	$data = $request->getParsedBody();

	require_once '../handlers/folder-files.php';
	require_once '../api/receive-document/classes.php';

	session_start();

	$id = $args['id'];
	
	# document_dt_add_params
	$document_dt_add_params = $data['document_dt_add_params'];
	unset($data['document_dt_add_params']);
	#

	# actions
	$actions = $data['actions'];
	unset($data['actions']);
	#	
	
	# files for deletion
	$delete_files = $data['delete_files'];
	unset($data['delete_files']);
	#
	
	$data['user_id'] = $_SESSION['itrack_user_id'];
	$data['origin'] = $data['origin']['id'];
	$data['doc_type'] = $data['doc_type']['id'];
	$data['communication'] = $data['communication']['id'];
	$data['document_transaction_type'] = $data['document_transaction_type']['id'];	

	$uploads = array("files"=>$data['files']);	
	unset($data['files']);

	$data['dt_add_params'] = json_encode($document_dt_add_params);

	unset($data['document_date_barcode']);
	
	$data['update_log'] = "CURRENT_TIMESTAMP";
	$con->updateData($data,'id');

	$con->table = "tracks";

	$actions_arr = array("for_initial"=>1,"for_signature"=>2,"for_routing"=>3);
	
	foreach ($actions as $i => $action) {

		$track = $con->getData("SELECT * FROM tracks WHERE document_id = $id AND track_action = ".$actions_arr[$i]);

		if ($action['value']) {

			if (count($track)) {
				
				$track = array(
					"id"=>$track[0]['id'],
					"track_action_add_params"=>json_encode($action['params'][0]),
					"track_action_status"=>null,
					"track_user"=>$_SESSION['itrack_user_id'],
					"update_log"=>"CURRENT_TIMESTAMP"
				);

				$con->updateData($track,'id');
			
			} else {
				
				$track = array(
					"document_id"=>$id,
					"office_id"=>$_SESSION['office'],
					"track_action"=>$actions_arr[$i],
					"track_action_add_params"=>json_encode($action['params'][0]),
					"track_action_status"=>null,
					"track_user"=>$_SESSION['itrack_user_id'],
				);
				
				$con->insertData($track);				
				
			};
		
		} else {
			
			if (count($track)) $con->deleteData(array("id"=>$track[0]['id']));
			
		};

		
	};

	if (count($delete_files)) {
		deleteFiles($con,$delete_files,"../files");
	};
	
	uploadFiles($con,$uploads,$data['barcode'],$id,"../files");	

});

$app->get('/for/initial/{id}', function ($request, $response, $args) {

	require_once '../path_url.php';
	require_once '../document-info.php';
	require_once 'datetime.php';
	require_once '../functions.php';
	
	$con = $this->con;
	$con->table = "documents";
	
	session_start();
	
	$id = $args['id'];
	
	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
	$document = document_info_complete($con,$document[0]);	
	
	$document['document_date'] = date("M j, Y h:i A",strtotime($document['document_date']));
	$due_date = due_date($document['document_date'],$document['document_transaction_type']['days']);
	$document['due_date'] = date("M j, Y h:i A",strtotime($due_date));
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	# tracks
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY system_log");
	$track = get_action_track($tracks,$session_user_id,$session_office);

	$action = get_staff_action($track,$session_user_id,$session_office);	
	
    return $this->view->render($response, 'initial.html', [
		'page'=>'initial',
		'path'=>$base_path,
		'url'=>"../".$base_url,
        'id'=>$args['id'],
		'document'=>$document,
		'track_param'=>$action,
    ]);

})->setName('document');

$app->get('/track/assess/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";

	require_once '../document-info.php';
	require_once '../functions.php';	

	session_start();
	
	$id = $args['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	# tracks
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id");	
	$track = get_action_track($tracks,$session_user_id,$session_office);

	$action = array("action"=>null,"staff"=>null,"ok"=>false);	
	
	if (count($track)) {

		$action = get_staff_action($track,$session_user_id,$session_office);
	
	};

	return $response->withJson($action);

});

$app->get('/for/initial/doc/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";	

	require_once '../handlers/folder-files.php';
	require_once '../functions.php';
	
	session_start();	
	
	$id = $args['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		
	
	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params FROM documents WHERE id = $id");	
	
	# tracks
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id");	
	$track = get_action_track($tracks,$session_user_id,$session_office);

	$initial = user_has_action_doc($con,$track,$session_user_id);

	$files = get_files("../files/",$document[0]['barcode']);

	return $response->withJson(array("files"=>$files,"track"=>$track,"initial"=>$initial));

});

$app->post('/for/initial/update', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	session_start();	

	$data = $request->getParsedBody();

	$id = $data['id'];

	$track = array(
		"document_id"=>$id,
		"office_id"=>$_SESSION['office'],
		"track_action_staff"=>$_SESSION['itrack_user_id'],		
		"track_action_status"=>"Initialed",
		"track_user"=>$_SESSION['itrack_user_id'],
		"preceding_track"=>$data['track']['id'],
	);

	$track_id = $data['track_id'];
	
	if ($data['initial']) {

		$insert_track = $con->insertData($track);
		$track_id = $con->insertId;

	} else {

		$delete_track = $con->deleteData(array("id"=>$track_id));

	};
	
	return $response->withJson($track_id);

});

$app->get('/filters', function($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";
	
	$filters = [];
	
	$con->table = "offices";
	$_offices = $con->all(['id','office','shortname']);
	
	$offices[] = array("id"=>0,"office"=>"All","shortname"=>"All");
	foreach ($_offices as $_office) {
		
		$offices[] = $_office;
		
	};
	
	$con->table = "communications";	
	$_communications = $con->all(['id','communication','shortname']);	
	
	$communications[] = array("id"=>0,"communication"=>"All","shortname"=>"All");
	foreach ($_communications as $_communication) {
		
		$communications[] = $_communication;
		
	};	
	
	$con->table = "transactions";	
	$_transactions = $con->all(['id','transaction','days']);	
	
	$transactions[] = array("id"=>0,"transaction"=>"All","days"=>"All");
	foreach ($_transactions as $_transaction) {
		
		$transactions[] = $_transaction;
		
	};		
	
	$con->table = "document_types";	
	$_doc_types = $con->all(['id','document_type']);	
	
	$doc_types[] = array("id"=>0,"document_type"=>"All");
	foreach ($_doc_types as $_doc_type) {
		
		$doc_types[] = $_doc_type;
		
	};	
	
	$filters = array("offices"=>$offices,"communications"=>$communications,"transactions"=>$transactions,"doc_types"=>$doc_types);
	
    return $response->withJson($filters);	

});

$app->get('/offices', function($request, $response, $args) {

	$con = $this->con;
	$con->table = "users";
	
	require_once '../system_setup.php';
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);

	$excluded_staffs = $setup->get_setup_as_string(6);
	
	$filters = [];
	
	$con->table = "offices";
	$_offices = $con->all(['id','office','shortname']);
	
	// $offices[] = array("id"=>0,"office"=>"All","shortname"=>"All","staffs"=>[]);
	$offices = [];
	foreach ($_offices as $_office) {
		
		if ($_office['id'] == 1) continue;
		
		$staffs = $con->getData("SELECT id, CONCAT_WS(' ',fname, lname) fullname FROM users WHERE div_id = ".$_office['id']." AND id NOT IN($excluded_staffs)");
		$_office['staffs'] = $staffs;
		
		$offices[] = $_office;
		
	};
	
    return $response->withJson($offices);	

});

$app->get('/office/staffs', function($request, $response, $args) {

	$con = $this->con;
	$con->table = "users";

	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		

	$staffs = $con->getData("SELECT id, CONCAT_WS(' ',fname, lname) fullname FROM users WHERE div_id = $session_office");

    return $response->withJson($staffs);	

});

$app->post('/filter', function($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";
	
	require_once '../document-info.php';	
	
	$data = $request->getParsedBody();	
	
	$criteria = ["origin","communication","document_transaction_type","doc_type"];
	
	$filters = "";
	foreach ($criteria as $i => $criterion) {

		if ($data[$criterion]['id']==0) continue;
		
		if ($filters=="") $filters.=" WHERE $criterion = ".$data[$criterion]['id'];
		else $filters.=" AND $criterion = ".$data[$criterion]['id'];

	};

	$documents = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date FROM documents$filters");
	foreach ($documents as $i => $document) {
		
		$documents[$i] = document_info($con,$document);
		$documents[$i]['document_date'] = date("M j, Y h:i A",strtotime($document['document_date']));		
		
	};
	
    return $response->withJson($documents);	

});

$app->get('/action/{id}', function ($request, $response, $args) {

	require_once '../path_url.php';
	require_once '../document-info.php';
	require_once 'datetime.php';
	require_once '../functions.php';
	
	$con = $this->con;
	$con->table = "documents";
	
	session_start();
	
	$id = $args['id'];

	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
	$document = document_info_complete($con,$document[0]);	
	
	$document['document_date'] = date("M j, Y h:i A",strtotime($document['document_date']));
	$due_date = due_date($document['document_date'],$document['document_transaction_type']['days']);
	$document['due_date'] = date("M j, Y h:i A",strtotime($due_date));
	
    return $this->view->render($response, 'action.html', [
		'page'=>'update-tracks',
		'path'=>$base_path,
		'url'=>$base_url,
        'id'=>$args['id'],
		'document'=>$document,
    ]);

})->setName('document');

$app->get('/doc/actions/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";	

	require_once '../handlers/folder-files.php';
	require_once '../functions.php';
	
	session_start();	
	
	$id = $args['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		
	
	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params FROM documents WHERE id = $id");	
	
	$files = get_files("../files/",$document[0]['barcode']);	
	
	# tracks for actions
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id AND track_action IS NOT NULL");

	$actions_arr = array(null,"Initialed","Approved");

	$actions = [];
	foreach ($tracks as $track) {
		
		if ($track['track_action']==4) continue;
		
		$staffs = get_staffs_actions($con,$track);
	
		$actions[] = array(
			"track_id"=>$track['id'],
			"track_action"=>$track['track_action'],
			"track_action_description"=>$actions_arr[$track['track_action']],
			"staffs"=>$staffs
		);

	};

	return $response->withJson(array("files"=>$files,"tracks"=>$tracks,"actions"=>$actions));

});

$app->post('/doc/actions/update', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	require_once '../document-actions.php';
	require_once '../system_setup.php';	
	require_once '../functions.php';
	require_once '../notify.php';

	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	session_start();	

	$data = $request->getParsedBody();

	$id = $data['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		

	$transit = array(
		"id"=>1,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>false,		
	);

	$document_actions = document_actions;

	$document_action_done_status = document_action_done_status($document_actions,$data['action']['track_action']);
	$track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>$data['staff']['id'],		
		"track_action_status"=>$document_action_done_status,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($transit),
		"preceding_track"=>$data['action']['track_id'],
	);

	$action_track_id = $data['staff']['action_track_id'];
	
	$status = ($session_office == $data['staff']['office']['id']);
	
	$res = array("action_track_id"=>$action_track_id,"status"=>$status);
	
	if ($status) {
	
		if ($data['staff']['done']) {

			$insert_track = $con->insertData($track);
			$action_track_id = $con->insertId;
			
			$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
			$all = $setup->get_setup_as_string(10);
			
			$admin_recipient = get_admin_recipient($con,$id);			
			
			# notify Liaisons AOs AAsts AAs
			if ($data['action']['track_action']==1) {
				
				notify($con,"initialed",array("doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status));
				
				# notify admin recipient
				notify($con,"initialed",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>$admin_recipient,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status),false);
				
			};
			
			if ($data['action']['track_action']==2) {

				notify($con,"approved",array("doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status));
				
				# notify admin recipient
				notify($con,"approved",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>$admin_recipient,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status),false);
			
			};
			

		} else {

			$delete_track = $con->deleteData(array("id"=>$action_track_id));

		};
		
	};

	return $response->withJson($res);

});

$app->post('/doc/actions/comment', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	require_once '../document-actions.php';
	require_once '../system_setup.php';	
	require_once '../functions.php';
	require_once '../notify.php';

	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	session_start();	

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

	$data = $request->getParsedBody();

	$id = $data['document']['id'];
	
	$track_transit = array(
		"id"=>1,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>false,		
	);

	$document_actions = document_actions;	
	
	$document_action_done_status = document_action_done_status($document_actions,4);
	$track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action"=>4,
		"track_action_staff"=>$data['comment']['staff']['id'],		
		"track_action_status"=>$document_action_done_status,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($track_transit),
		"comment"=>$data['comment']['text'],
	);

	$insert_track = $con->insertData($track);
	$track_id = $con->insertId;

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");	

	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"commented",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['comment']['staff']['id'],"track_action_status"=>$document_action_done_status));

	return $response->withJson([]);

});

$app->post('/doc/transit/pickup', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	require_once '../document-transit.php';
	require_once '../system_setup.php';
	require_once '../functions.php';
	require_once '../notify.php';	
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	session_start();	

	$data = $request->getParsedBody();

	$id = $data['document']['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	$pick_up = 2;
	
	$track_transit = array(
		"id"=>$pick_up,
		"picked_up_by"=>$data['transit']['staff']['id'],
		"received_by"=>null,
		"office"=>$data['transit']['office']['id'],
		"released_to"=>null,
		"filed"=>false,		
	);

	$transit = transit;

	$transit_description = transit_description($transit,$pick_up);
	$track = array(
		"document_id"=>$id,
		"office_id"=>$_SESSION['office'],
		"track_action_staff"=>$data['transit']['staff']['id'],		
		"track_action_status"=>$transit_description,
		"track_user"=>$_SESSION['itrack_user_id'],
		"transit"=>json_encode($track_transit),
	);

	$insert_track = $con->insertData($track);
	$track_id = $con->insertId;

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");	

	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"picked_up",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['transit']['staff']['id'],"track_action_status"=>$transit_description));
	
	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);
	notify($con,"picked_up",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['transit']['staff']['id'],"track_action_status"=>$transit_description),false);
	
	// return $response->withJson([]);

});

$app->post('/doc/transit/receive/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	require_once '../document-transit.php';
	require_once '../system_setup.php';
	require_once '../functions.php';
	require_once '../notify.php';

	$system_setup = system_setup;
	$setup = new setup($system_setup);

	session_start();

	$data = $request->getParsedBody();	
	
	$id = intval($args['id']);

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	$receive = 3;

	$track_transit = array(
		"id"=>$receive,
		"picked_up_by"=>null,
		"received_by"=>$session_user_id,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>$data['file'],		
	);

	$transit = transit;

	$transit_description = transit_description($transit,$receive);	
	$track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>$session_user_id,		
		"track_action_status"=>$transit_description,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($track_transit),
	);

	$insert_track = $con->insertData($track);
	$track_id = $con->insertId;

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");	

	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"received",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"filed"=>$data['file']));

	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);	
	notify($con,"received",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"filed"=>$data['file']),false);
	
	// return $response->withJson([]);

});

$app->post('/doc/transit/release', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	require_once '../document-transit.php';
	require_once '../system_setup.php';
	require_once '../functions.php';
	require_once '../notify.php';	
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

	$data = $request->getParsedBody();

	$id = $data['document']['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

	$release = 4;

	$track_transit = array(
		"id"=>$release,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>null,
		"released_to"=>$data['release']['staff']['id'],
		"filed"=>false,		
	);

	$transit = transit;

	$transit_description = transit_description($transit,$release);
	$track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>$session_user_id,
		"track_action_status"=>$transit_description,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($track_transit),
	);

	$insert_track = $con->insertData($track);
	$track_id = $con->insertId;

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");	

	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"released",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"track_office"=>$session_office,"release_to"=>$data['release']['staff']['id']));
	
	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);
	notify($con,"released",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"track_office"=>$session_office,"release_to"=>$data['release']['staff']['id']),false);
	
	// return $response->withJson([]);

});

$app->post('/doc/transit/file/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "tracks";

	require_once '../document-transit.php';
	require_once '../system_setup.php';
	require_once '../functions.php';
	require_once '../notify.php';	
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	

	session_start();

	$data = $request->getParsedBody();	
	
	$id = intval($args['id']);

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	$file = 5;

	$track_transit = array(
		"id"=>$file,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>true,		
	);

	$transit = transit;

	$transit_description = transit_description($transit,$file);	
	$track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>$session_user_id,		
		"track_action_status"=>$transit_description,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($track_transit),
	);

	$insert_track = $con->insertData($track);
	$track_id = $con->insertId;

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");	

	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"filed",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"filed"=>true));
	
	// return $response->withJson([]);

});

$app->get('/doc/track/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";

	require_once '../document-info.php';
	require_once 'datetime.php';
	require_once '../functions.php';
	require_once '../system_setup.php';		
	require_once '../tracks.php';
	// require_once '../document-transit.php';
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	// $transit = transit;
	
	session_start();

	$id = $args['id'];	

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
	$document = document_info_complete($con,$document[0]);	

	$document['document_date'] = date("M j, Y h:i A",strtotime($document['document_date']));
	$due_date = due_date($document['document_date'],$document['document_transaction_type']['days']);
	$document['due_date'] = date("M j, Y h:i A",strtotime($due_date));

	$document['tracks'] = tracks($con,$setup,$id,$document);

	return $response->withJson($document);

});

$app->run();

?>