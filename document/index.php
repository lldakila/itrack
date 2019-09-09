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
	
	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params, is_rush FROM documents WHERE id = $id");
	
	if (count($document)) {
		
		$staff = $con->getData("SELECT CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$document[0]['user_id']);
		$document[0]['receiver'] = $staff[0]['fullname'];
		unset($document[0]['user_id']);

		$document[0]['dt_add_params'] = json_decode($document[0]['dt_add_params'],false);
		$document[0]['document_dt_add_params'] = $document[0]['dt_add_params'];

		$document[0]['document_date_barcode'] = date("M j, Y h:i:s A",strtotime($document[0]['document_date']));
		$document[0]['date'] = date("M j, Y",strtotime($document[0]['document_date']));
		$document[0]['time'] = date("h:i:s A",strtotime($document[0]['document_date']));

		$document[0]['is_rush'] = ($document[0]['is_rush'])?true:false;
		
		$document[0]['files'] = [];
		$document[0]['delete_files'] = [];

		// $files = get_files("../files/",$document[0]['barcode']);
		$files = get_document_files($con,"/files/","../files/",$document[0]['id']);
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

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

	$id = $args['id'];

	$document_actions = document_actions;

	# tracks
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id AND office_id = $session_office");

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

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

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
	
	$data['is_rush'] = ($data['is_rush'])?1:0;

	$uploads = array("files"=>$data['files']);	
	unset($data['files']);

	$data['dt_add_params'] = json_encode($document_dt_add_params);

	unset($data['document_date_barcode']);
	unset($data['receiver']);
	unset($data['date']);
	unset($data['time']);
	
	$data['update_log'] = "CURRENT_TIMESTAMP";
	$con->updateData($data,'id');

	$con->table = "tracks";

	$actions_arr = array("for_initial"=>1,"for_signature"=>2,"for_routing"=>3,"comment"=>4,"revise"=>5,"revised"=>6);

	foreach ($actions as $i => $action) {
		
		if ($actions_arr[$i] == 4) continue; // skip comment
		if ($actions_arr[$i] == 5) continue; // skip revise
		if ($actions_arr[$i] == 6) continue; // skip revised
		
		$sql = "SELECT * FROM tracks WHERE document_id = $id AND office_id = $session_office AND track_action = ".$actions_arr[$i];

		$track = $con->getData($sql);

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
				
				# transit
				$transit = array(
					"id"=>1,
					"picked_up_by"=>null,
					"received_by"=>null,
					"office"=>$session_office,
					"released_to"=>null,
					"filed"=>false,
				);				
				
				$track = array(
					"document_id"=>$id,
					"office_id"=>$_SESSION['office'],
					"track_action"=>$actions_arr[$i],
					"track_action_add_params"=>json_encode($action['params'][0]),
					"track_action_status"=>null,
					"track_user"=>$_SESSION['itrack_user_id'],
					"transit"=>json_encode($transit)
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
	
	uploadFiles($con,$uploads,$data['barcode'],$id,"../files",false);

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
	$due_date = due_date($con,$id,null);
	$document['due_date'] = ($due_date=="file")?$due_date:date("M j, Y h:i A",strtotime($due_date));

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
	require_once '../system_setup.php';	

	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	$con = $this->con;
	$con->table = "documents";
	
	session_start();
	
	$id = $args['id'];

	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
	$document = document_info_complete($con,$document[0]);	
	
	$document['document_date'] = date("M j, Y h:i A",strtotime($document['document_date']));
	$due_date = due_date($con,$id,$setup);
	$document['due_date'] = ($due_date=="filed")?$due_date:date("M j, Y h:i A",strtotime($due_date));
	$document['current_location'] = document_current_location($con,$id);	
	
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
	
	// $files = get_files("../files/",$document[0]['barcode']);	
	$files = get_document_files($con,"/files/","../files/",$document[0]['id']);	
	
	# tracks for actions
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id AND office_id = $session_office AND track_action IS NOT NULL");

	$actions_arr = array(null,"Initialed","Approved");

	$actions = [];
	foreach ($tracks as $track) {
		
		if ($track['track_action']==4) continue; # skip comment
		if ($track['track_action']==5) continue; # skip revise
		if ($track['track_action']==6) continue; # skip revised
		
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
			
			# PA staffs
			$pa_staffs = $setup->get_setup_as_string(11);
			
			$admin_recipient = get_admin_recipient($con,$id);

			# notify Liaisons AOs AAsts AAs in originating office
			if ($data['action']['track_action']==1) {
				
				notify($con,"initialed",array("doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status,"pa_staffs"=>$pa_staffs,"setup"=>$setup));
				
				# notify admin recipient
				notify($con,"initialed",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status,"pa_staffs"=>$pa_staffs,"setup"=>$setup),false);
				
				# notify PA staffs
				$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
				foreach ($notify_pa_staffs as $nps) {
					
					if ($nps['id']==$session_office) continue;					
					if ($nps['id']==$admin_recipient) continue;					
					notify($con,"initialed",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status,"pa_staffs"=>$pa_staffs,"setup"=>$setup),false);
					
				};
				
			};
			
			if ($data['action']['track_action']==2) {

				notify($con,"approved",array("doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status,"pa_staffs"=>$pa_staffs,"setup"=>$setup));
				
				# notify admin recipient
				notify($con,"approved",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status,"pa_staffs"=>$pa_staffs,"setup"=>$setup),false);
			
				# notify PA staffs
				$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
				foreach ($notify_pa_staffs as $nps) {

					if ($nps['id']==$session_office) continue;					
					if ($nps['id']==$admin_recipient) continue;		
					notify($con,"approved",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$action_track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['staff']['id'],"track_action_status"=>$document_action_done_status,"pa_staffs"=>$pa_staffs,"setup"=>$setup),false);

				};
			
			};
			

		} else {

			$delete_track = $con->deleteData(array("id"=>$action_track_id));

		};
		
	};

	return $response->withJson($res);

});

$app->post('/doc/actions/revisions/verify', function ($request, $response, $args) {

	require_once '../functions.php';

	function document_actions($con,$id,$session_office) {

		$sql = "SELECT track_action_add_params FROM tracks WHERE document_id = $id AND office_id = $session_office";
		$actions = $con->getData($sql);

		$document_actions = array("for_initial"=>false,"for_approval"=>false);
		
		foreach ($actions as $action) {
			
			$action_arr = json_decode($action['track_action_add_params'], true);
			
			if ($action_arr['action_id'] == 1) $document_actions['for_initial'] = true;
			if ($action_arr['action_id'] == 2) $document_actions['for_approval'] = true;

		};

		return $document_actions;

	};

	function initialed($con,$id,$track_id) {

		$initialed = false;

		$sql = "SELECT * FROM tracks WHERE document_id = $id AND preceding_track = $track_id";
		$tracks = $con->getData($sql);

		foreach ($tracks as $track) {

			if ($track['track_action_status']=="initialed") $initialed = true;

		};
		
		return $initialed;
		
	};
	
	function approved($tracks,$session_office) {
		
		$approved = false;
		
		foreach ($tracks as $track) {
			
			if (($track['track_action_status']=="approved") && ($track['office_id']==$session_id)) $approved = true;
			
		};
		
		return $approved;
		
	};
	
	function for_initial_track_id($tracks,$session_office) {
		
		$track_id = 0;
		
		foreach ($tracks as $track) {
			
			if (($track['track_action'] == 1) && ($track['office_id']==$session_office)) $track_id = $track['id'];
			
		};
		
		return $track_id;
		
	};

	function for_approval_track_id($tracks,$session_office) {
		
		$track_id = 0;
		
		foreach ($tracks as $track) {
			
			if (($track['track_action'] == 2) && ($track['office_id']==$session_office)) $track_id = $track['id'];
			
		};
		
		return $track_id;
		
	};	
	
	$con = $this->con;
	$con->table = "tracks";

	$data = $request->getParsedBody();

	$id = $data['id'];
	$track_action = $data['action']['track_action'];

	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];

	# action tracks
	$sql = "SELECT * FROM tracks WHERE document_id = $id AND track_action = $track_action AND office_id = $session_office"; # for initial/approve
	$action_track = $con->getData($sql);

	$sql = "SELECT * FROM tracks WHERE document_id = $id";
	$tracks = $con->getData($sql);

	$sql = "SELECT * FROM revisions WHERE document_id = $id";
	$revisions = $con->getData($sql);

	$all_ok = ["true"];
	$notify = null;
	$status = false;
	switch ($track_action) {
		
		case 1: # for initial
			
			# check the office
			if (get_transit_office_id($con,$action_track[0]['transit'])!=$session_office) {
				
				$verification = array(
					"notify"=>"You cannot update this action, it belongs to a different office",
					"status"=>false
				);

				return $response->withJson($verification);				
				
			};
			
			foreach ($revisions as $revision) {
				
				$all_ok[] = ($revision['revision_ok'])?"true":"false";
				
			};

			$is_all_ok = implode("&&",$all_ok);			
			$status = eval("return $is_all_ok;");
			if (count($revisions)==0) $status = true;			
			if (!$status) $notify = "This document cannot be flagged as initialed if there are revisions that were not updated.  Please make sure all revisions are updated.";

		break;
		
		case 2: # for approval
			
			# check the office
			if (get_transit_office_id($con,$action_track[0]['transit'])!=$session_office) {
				
				$verification = array(
					"notify"=>"You cannot update this action, it belongs to a different office",
					"status"=>false
				);

				return $response->withJson($verification);				
				
			};			
			
			if ( (document_actions($con,$id,$session_office)['for_initial']) && (!initialed($con,$id,for_initial_track_id($tracks,$session_office))) ) {
				
				$notify = "This document cannot be flagged as approved, it must be flagged as initialed first.";
				
			} else {
				
				foreach ($revisions as $revision) {
					
					$all_ok[] = ($revision['revision_ok'])?"true":"false";
					
				};

				$is_all_ok = implode("&&",$all_ok);			
				$status = eval("return $is_all_ok;");
				if (count($revisions)==0) $status = true;
				if (!$status) $notify = "This document cannot be flagged as approved if there are revisions that were not updated.  Please make sure all revisions are updated.";
				
			};
			
		break;
		
	};	
	
	$verification = array(
		"notify"=>$notify,
		"status"=>$status
	);

	return $response->withJson($verification);	
	
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

	# notify Liaisons AOs AAsts AAs in originating office
	$all = $setup->get_setup_as_string(10);
	notify($con,"commented",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['comment']['staff']['id'],"track_action_status"=>$document_action_done_status));

	# notify PA Staffs
	$pa_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
	foreach ($notify_pa_staffs as $nps) {
		
		notify($con,"commented",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['comment']['staff']['id'],"track_action_status"=>$document_action_done_status),false);
		
	};

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

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");	
	$transit = transit;

	// add release track
	
/* 	$release = 4;

	$release_track_transit = array(
		"id"=>$release,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$data['transit']['office']['id'],
		"released_to"=>null,
		"filed"=>false,		
	);

	$release_transit_description = transit_description($transit,$release);
	$release_track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>$session_user_id,
		"track_action_status"=>$release_transit_description,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($release_track_transit),
	);

	$insert_release_track = $con->insertData($release_track);
	$release_track_id = $con->insertId;	
	
	# notify
	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"released",array("doc_id"=>$id,"track_id"=>$release_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$release_transit_description,"track_office"=>$session_office,"release_to"=>$data['transit']['office']['id']));
	
	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);
	notify($con,"released",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$release_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$release_transit_description,"track_office"=>$session_office,"release_to"=>$data['transit']['office']['id']),false); */
	
	//

	// add pick up track
	
	$pick_up = 2;
	
	$pick_up_track_transit = array(
		"id"=>$pick_up,
		"picked_up_by_other"=>(isset($data['transit']['other']))?$data['transit']['other']:null,
		"picked_up_by"=>$data['transit']['staff']['id'],
		"received_by"=>null,
		"office"=>$data['transit']['office']['id'],
		"released_to"=>null,
		"filed"=>false,		
	);

	$pick_up_transit_description = transit_description($transit,$pick_up);
	$pick_up_track = array(
		"document_id"=>$id,
		"office_id"=>$_SESSION['office'],
		"track_action_staff"=>$data['transit']['staff']['id'],		
		"track_action_status"=>$pick_up_transit_description,
		"track_user"=>$_SESSION['itrack_user_id'],
		"transit"=>json_encode($pick_up_track_transit),
	);

	$insert_pick_up_track = $con->insertData($pick_up_track);
	$pick_up_track_id = $con->insertId;

	# notify
	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"picked_up",array("doc_id"=>$id,"track_id"=>$pick_up_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['transit']['staff']['id'],"track_action_status"=>$pick_up_transit_description));
	
	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);
	notify($con,"picked_up",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$pick_up_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['transit']['staff']['id'],"track_action_status"=>$pick_up_transit_description),false);
	
	# notify PA staffs
	$pa_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
	foreach ($notify_pa_staffs as $nps) {
		
		if ($nps['id']==$session_office) continue;					
		if ($nps['id']==$admin_recipient) continue;					
		notify($con,"picked_up",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$pick_up_track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['transit']['staff']['id'],"track_action_status"=>$pick_up_transit_description),false);
		
	};

	//
	
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
	
	// check if document is already received
	$sql = "SELECT * FROM tracks WHERE document_id = $id AND office_id = $session_office AND track_action_status = 'received'";
	$is_received = $con->getData($sql);
		
	if (count($is_received)) return $response->write(1);
	
	// check if document is picked up
	$sql = "SELECT * FROM tracks WHERE document_id = $id AND track_action_status = 'picked up'";
	$is_picked_up = $con->getData($sql);
	
	if (count($is_picked_up)) {
		// check if picked up by office
		if (get_transit_office_id($con,$is_picked_up[0]['transit'])==$session_office) return $response->write(2);
	}
	
	$transit = transit;	
	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");		
	
	// add release track
	
/* 	$release = 4;

	$release_track_transit = array(
		"id"=>$release,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>false,		
	);

	$release_transit_description = transit_description($transit,$release);
	$release_track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>0,
		"track_action_status"=>$release_transit_description,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($release_track_transit),
	);

	$insert_release_track = $con->insertData($release_track);
	$release_track_id = $con->insertId;	
	
	# notify
	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"released",array("doc_id"=>$id,"track_id"=>$release_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>0,"track_action_status"=>$release_transit_description,"track_office"=>$session_office,"release_to"=>$session_office));
	
	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);
	notify($con,"released",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$release_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>0,"track_action_status"=>$release_transit_description,"track_office"=>$session_office,"release_to"=>$session_office),false);	 */
	
	//
	
	// add receive track
	
	$receive = 3;

	$receive_track_transit = array(
		"id"=>$receive,
		"picked_up_by"=>null,
		"received_by"=>$session_user_id,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>$data['file'],		
	);

	$receive_transit_description = transit_description($transit,$receive);	
	$receive_track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action_staff"=>$session_user_id,		
		"track_action_status"=>$receive_transit_description,
		"track_user"=>$session_user_id,
		"transit"=>json_encode($receive_track_transit),
	);

	$insert_receive_track = $con->insertData($receive_track);
	$receive_track_id = $con->insertId;
	
	# notify
	# notify Liaisons AOs AAsts AAs
	$all = $setup->get_setup_as_string(10);
	notify($con,"received",array("doc_id"=>$id,"track_id"=>$receive_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$receive_transit_description,"filed"=>$data['file']));

	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);	
	notify($con,"received",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$receive_track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$receive_transit_description,"filed"=>$data['file']),false);
	
	# notify PA staffs
	$pa_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
	foreach ($notify_pa_staffs as $nps) {
		
		if ($nps['id']==$session_office) continue;					
		if ($nps['id']==$admin_recipient) continue;					
		notify($con,"received",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$receive_track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$receive_transit_description,"filed"=>$data['file']),false);
		
	};	
	
	//	
	// return $response->withJson([]);
	return $response->write(0);	

});

$app->get('/doc/transit/is_receive/{id}', function ($request, $response, $args) {

	require_once '../system_setup.php';
	require_once '../functions.php';

	$system_setup = system_setup;
	$setup = new setup($system_setup);	

	$initial_office = $setup->get_setup_as_string(4);	

	$con = $this->con;
	$con->table = "tracks";

	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

	$id = intval($args['id']);

	$sql = "SELECT * FROM tracks WHERE document_id = $id AND office_id = $session_office AND track_action_status = 'received'";
	$received = $con->getData($sql);	
	$already_received = count($received);

	// mark received if document is picked up
	// check if document is picked up
	$sql = "SELECT * FROM tracks WHERE document_id = $id AND track_action_status = 'picked up'";
	$is_picked_up = $con->getData($sql);
	
	if (count($is_picked_up)) {
		// check if picked up by office
		if (get_transit_office_id($con,$is_picked_up[0]['transit'])==$session_office) $already_received = 1;
	}

	if ($session_office == $initial_office) {
		
		$sql = "SELECT * FROM tracks WHERE document_id = $id AND office_id = $session_office AND track_action IN (1,2)";
		$received = $con->getData($sql);
		$already_received = count($received);		
		
	};

	return $response->write($already_received);

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

	$data = $request->getParsedBody();

	$id = $data['document']['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	

	$release = 4;

	$track_transit = array(
		"id"=>$release,
		"picked_up_by"=>null,
		"received_by"=>null,
		// "office"=>$data['release']['office']['id'],
		"office"=>$session_office,
		"release_to_office"=>$data['release']['office']['id'],
		// "released_to"=>$data['release']['staff']['id'],
		"released_to"=>null,
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
	notify($con,"released",array("doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"track_office"=>$session_office,"release_to"=>$data['release']['office']['id']));
	
	# notify admin recipient
	$admin_recipient = get_admin_recipient($con,$id);
	notify($con,"released",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"track_office"=>$session_office,"release_to"=>$data['release']['office']['id']),false);
	
	# notify PA staffs
	$pa_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
	foreach ($notify_pa_staffs as $nps) {
		
		if ($nps['id']==$session_office) continue;					
		if ($nps['id']==$admin_recipient) continue;					
		notify($con,"released",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"track_office"=>$session_office,"release_to"=>$data['release']['office']['id']),false);
		
	};	
	
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
	
	# notify PA staffs
	$pa_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
	foreach ($notify_pa_staffs as $nps) {

		notify($con,"filed",array("notify_user"=>$nps['id'],"doc_id"=>$id,"track_id"=>$track_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$session_user_id,"track_action_status"=>$transit_description,"filed"=>true),false);

	};

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

	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date, is_rush FROM documents WHERE id = $id");
	$document = document_info_complete($con,$document[0]);	

	$document['document_date'] = date("M j, Y h:i A",strtotime($document['document_date']));
	$due_date = due_date($con,$id,$setup);
	$document['due_date'] = ($due_date=="filed")?$due_date:date("M j, Y h:i A",strtotime($due_date));
	$document['current_location'] = document_current_location($con,$id);

	$document['tracks'] = tracks($con,$setup,$id,$document);

	return $response->withJson($document);

});

$app->get('/doc/revisions/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "revisions";

	// require_once '../handlers/folder-files.php';
	// require_once '../functions.php';
	
	session_start();	
	
	$id = $args['id'];

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		

	$revisions = $con->getData("SELECT * FROM revisions WHERE document_id = $id");
	
	foreach ($revisions as $i => $revision) {
		
		$revisions[$i]['revision_ok'] = ($revision['revision_ok'])?true:false;
		
		$revisions[$i]['datetime'] = date("M j, Y h:i A",strtotime($revision['system_log']));
		$revisions[$i]['datetime_completed'] = ($revision['datetime_completed']!=null)?date("M j, Y h:i A",strtotime($revision['datetime_completed'])):"";
		
	};

	return $response->withJson($revisions);

});

$app->post('/doc/revisions/add/{id}', function ($request, $response, $args) {

	require_once '../system_setup.php';	
	require_once '../functions.php';
	require_once '../notify.php';

	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);		
	
	$con = $this->con;
	$con->table = "revisions";

	$id = $args['id'];
	$data = $request->getParsedBody();
	
	$data['revision_ok'] = (isset($data['revision_ok']))?($data['revision_ok'])?1:0:0;
	
	$data['user_id'] = $data['user_id']['id'];
	
	if ($data['id']) {
		
		$data['update_log'] = "CURRENT_TIMESTAMP";
		$update = $con->updateData($data,'id');
		
	} else {
		
		$data['document_id'] = $id;
		
		unset($data['id']);
		$insert = $con->insertData($data);		
		
		$revision_id = $con->insertId;
		
		$all = $setup->get_setup_as_string(10);		
		$admin_recipient = get_admin_recipient($con,$id);		
		
		$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
		$track_action_status = "added revisions to your document";
		
		# notify Liaisons AOs AAsts AAs		
		notify($con,"add_revision",array("doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status));
		
		# notify admin recipient
		notify($con,"add_revision",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$admin_recipient,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status),false);
		
		$track_transit = array(
			"id"=>1,
			"picked_up_by"=>null,
			"received_by"=>null,
			"office"=>$session_office,
			"released_to"=>null,
			"filed"=>false,		
		);		
		
		# update tracks
		$data = array(
			"document_id"=>$id,
			"office_id"=>$session_office,
			"track_action"=>5,
			"track_action_staff"=>$data['user_id'],
			"track_action_status"=>"added revisions",
			"track_user"=>$session_user_id,
			"transit"=>json_encode($track_transit),
			"revision_id"=>$revision_id,
		);
		add_track($con,$data);
		
	};

});

$app->get('/doc/revisions/edit/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "revisions";

	$id = $args['id'];

	$revision = $con->getData("SELECT id, user_id, notes, revision_ok FROM revisions WHERE document_id = $id");
	
	$user_id = ($revision[0]['user_id']==null)?0:$revision[0]['user_id'];
	$staff = $con->getData("SELECT id, CONCAT_WS(' ',fname, lname) fullname FROM users WHERE id = ".$user_id);	

	if (count($staff)) {
		$revision[0]['user_id'] = $staff[0];
	} else {
		unset($revision[0]['user_id']);
	};
	
	return $response->withJson($revision[0]);	

});

$app->put('/doc/revisions/update/{id}', function ($request, $response, $args) {

	require_once '../system_setup.php';	
	require_once '../functions.php';
	require_once '../notify.php';

	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	$con = $this->con;
	$con->table = "revisions";

	$id = $args['id']; // document id
	$data = $request->getParsedBody();

	unset($data['datetime']);
	unset($data['system_log']);
	unset($data['revision_ok']);
	
	$data['user_id'] = $data['user_id']['id'];	
	
	$data['update_log'] = "CURRENT_TIMESTAMP";
	$update = $con->updateData($data,'id');

	# update notifications
	$revision_id = $data['id'];

	$all = $setup->get_setup_as_string(10);		
	$admin_recipient = get_admin_recipient($con,$id);		
	
	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
	$track_action_status = "added revisions to your document";
	
	# notify Liaisons AOs AAsts AAs		
	notify($con,"add_revision",array("doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status));
	
	# notify admin recipient
	notify($con,"add_revision",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$admin_recipient,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status),false);

	$track_transit = array(
		"id"=>1,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>false,		
	);		
	
	# update tracks
	$data = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action"=>5,
		"track_action_staff"=>$data['user_id'],
		"track_action_status"=>"added revisions",
		"track_user"=>$session_user_id,
		"transit"=>json_encode($track_transit),
		"revision_id"=>$revision_id,
	);
	add_track($con,$data);	
	
});

$app->put('/doc/revisions/update/status/{id}', function ($request, $response, $args) {

	require_once '../system_setup.php';	
	require_once '../functions.php';
	require_once '../notify.php';

	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	
	
	$con = $this->con;
	$con->table = "revisions";

	$id = $args['id']; // document id
	$data = $request->getParsedBody();
	
	$data['revision_ok'] = ($data['revision_ok'])?1:0;
	$data['datetime_completed'] = ($data['revision_ok']>0)?"CURRENT_TIMESTAMP":null;

	$update = $con->updateData(array("id"=>$data['id'],"update_log"=>"CURRENT_TIMESTAMP","datetime_completed"=>$data['datetime_completed'],"revision_ok"=>$data['revision_ok']),'id');

	if ($data['revision_ok']) {

		$revision_id = $data['id'];

		$all = $setup->get_setup_as_string(10);		
		$admin_recipient = get_admin_recipient($con,$id);		

		$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
		$track_action_status = "Document revisions that were added on ".$data['datetime']." were marked okay";

		# notify Liaisons AOs AAsts AAs
		notify($con,"revision_ok",array("doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status));

		# notify admin recipient
		notify($con,"revision_ok",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$admin_recipient,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status),false);
		
		$track_transit = array(
			"id"=>1,
			"picked_up_by"=>null,
			"received_by"=>null,
			"office"=>$session_office,
			"released_to"=>null,
			"filed"=>false,		
		);		
		
		# update tracks
		$data = array(
			"document_id"=>$id,
			"office_id"=>$session_office,
			"track_action"=>6,
			"track_action_staff"=>$data['user_id'],
			"track_action_status"=>"revisions ok",
			"track_user"=>$session_user_id,
			"transit"=>json_encode($track_transit),
			"revision_id"=>$revision_id,
		);
		add_track($con,$data);		

	};
	
});

# delete revision
$app->delete('/doc/revisions/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "revisions";

	$id = $args['id'];
	
	$sql = "SELECT * FROM revisions WHERE id = $id";
	$revision = $con->getData($sql);
	
	$uploaded_file = $revision[0]['uploaded_file'];
	$dir = "../revisions/$id/";
	
	$file = $dir.$uploaded_file;
	
	if (file_exists($file)) unlink($file);
	
	$con->deleteData(array("id"=>$id));		

});

# upload files
$app->put('/doc/revisions/upload/files', function ($request, $response, $args) {
	
	session_start();
	
	$con = $this->con;
	$con->table = "files";
	
	$data = $request->getParsedBody();
	
	require_once '../handlers/folder-files.php';
	require_once '../api/receive-document/classes.php';
	
	uploadFiles($con,array("files"=>$data['files']),$data['barcode'],$data['id'],"../files",false);	

});

# delete file
$app->delete('/doc/revisions/delete/files/{id}/{name}', function (Request $request, Response $response, array $args) {

	require_once '../handlers/folder-files.php';
	require_once '../api/receive-document/classes.php';

	$con = $this->con;
	$con->table = "files";

	$id = $args['id'];
	$file_name = $args['name'];
	
	deleteFiles($con,[array("id"=>$id,"file_name"=>$file_name)],"../files");

});

# delete file
$app->delete('/doc/tracks/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "tracks";

	$id = $args['id'];

	$con->deleteData(array("id"=>$id));

});

$app->post('/doc/office/action', function (Request $request, Response $response, array $args) {

	require_once '../system_setup.php';
	require_once '../functions.php';
	require_once '../notify.php';

	$con = $this->con;
	$con->table = "tracks";

	$data = $request->getParsedBody();		
	
	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];

	$actions = $data['actions'];

	$actions_arr = array("for_initial"=>1,"for_signature"=>2,"for_routing"=>3,"comment"=>4,"revise"=>5,"revised"=>6);

	foreach ($actions as $i => $action) {
		
		if ($actions_arr[$i] == 4) continue; // skip comment
		if ($actions_arr[$i] == 5) continue; // skip revise
		if ($actions_arr[$i] == 6) continue; // skip revised		
		
		$sql = "SELECT * FROM tracks WHERE document_id = ".$data['id']." AND office_id = $session_office AND track_action = ".$actions_arr[$i];
		
		$track = $con->getData($sql);	
		
		if ($action['value']) {

			if (count($track)) {
				
				$track = array(
					"id"=>$track[0]['id'],
					"track_action_add_params"=>json_encode($action['params'][0]),
					"track_action_status"=>null,
					"track_user"=>$session_user_id,
					"update_log"=>"CURRENT_TIMESTAMP"
				);

				$con->updateData($track,'id');
			
			} else {
				
				# transit
				$transit = array(
					"id"=>1,
					"picked_up_by"=>null,
					"received_by"=>null,
					"office"=>$session_office,
					"released_to"=>null,
					"filed"=>false,
				);
				
				$track = array(
					"document_id"=>$data['id'],
					"office_id"=>$session_office,
					"track_action"=>$actions_arr[$i],
					"track_action_add_params"=>json_encode($action['params'][0]),
					"track_action_status"=>null,
					"track_user"=>$session_user_id,
					"transit"=>json_encode($transit)
				);				
				
				$con->insertData($track);				
				
			};
		
		} else {
			
			if (count($track)) $con->deleteData(array("id"=>$track[0]['id']));
			
		};
	
	};

});

$app->post('/doc/revisions/upload/{id}/{ext}', function (Request $request, Response $response, array $args) {

	$id = $args['id'];
	$filename = $args['ext'];
	
	require_once '../system_setup.php';	
	require_once '../functions.php';
	require_once '../notify.php';
	require_once 'folder-files.php';

	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);		
	
	$con = $this->con;
	$con->table = "revisions";

	$data = $request->getParsedBody();	

	$data['document_id'] = $id;
	$data['user_id'] = $session_user_id;
	$data['uploaded_file'] = $filename;
	
	$insert = $con->insertData($data);		
	
	$revision_id = $con->insertId;

	# upload file
	$dir = "../revisions/";
	$folder = "$dir/$id";
	if (!folder_exist($folder)) mkdir($folder);
	move_uploaded_file($_FILES['file']['tmp_name'],$folder."/$filename");	
	
	$all = $setup->get_setup_as_string(10);		
	$admin_recipient = get_admin_recipient($con,$id);		
	
	$document = $con->getData("SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, remarks, document_date FROM documents WHERE id = $id");
	$track_action_status = "added revisions to your document";
	
	# notify Liaisons AOs AAsts AAs		
	notify($con,"add_revision",array("doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$all,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status));
	
	# notify admin recipient
	notify($con,"add_revision",array("notify_user"=>$admin_recipient,"doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>$admin_recipient,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status),false);
	
	# notify PA staffs
	$pa_staffs = $setup->get_setup_as_string(11);
	$notify_pa_staffs = get_staffs_by_group_only($con,$pa_staffs);
	foreach ($notify_pa_staffs as $nps) {
		
		if ($nps['id']==$session_office) continue;					
		if ($nps['id']==$admin_recipient) continue;					
		notify($con,"add_revision",array("notify_user"=>$nps['id'],"doc_id"=>$id,"revision_id"=>$revision_id,"header"=>$document[0]['doc_name'],"group"=>0,"office"=>$document[0]['origin'],"track_action_staff"=>$data['user_id'],"track_action_status"=>$track_action_status),false);
		
	};
	
	$track_transit = array(
		"id"=>1,
		"picked_up_by"=>null,
		"received_by"=>null,
		"office"=>$session_office,
		"released_to"=>null,
		"filed"=>false,		
	);
	
	# update tracks
	$track = array(
		"document_id"=>$id,
		"office_id"=>$session_office,
		"track_action"=>5,
		"track_action_staff"=>$data['user_id'],
		"track_action_status"=>"added revisions",
		"track_user"=>$session_user_id,
		"transit"=>json_encode($track_transit),
		"revision_id"=>$revision_id,
	);
	add_track($con,$track);

});

$app->get('/doc/revisions/preview/{id}/{file}', function (Request $request, Response $response, array $args) {
	
	$base = str_replace("\\","/",__DIR__);			
	
	$id = $args['id'];
	$uploaded_file = $args['file'];

	$revision_dir = "/../revisions/$id/";					
	$revision_file = $base.$revision_dir.$uploaded_file;
	
	$file = explode(".",$uploaded_file);
	$ext = $file[count($file)-1];
	
	if (file_exists($revision_file)) {
		
		$type = mime_content_type($revision_file);
		$revision_file_data_uri = 'data:'.$type.';base64,'.base64_encode(file_get_contents($revision_file));
		
		switch ($ext) {
			
			case "jpg":
			
				echo "<img src='".$revision_file_data_uri."'>";
			
			break;
			
			case "png":

				echo "<img src='".$revision_file_data_uri."'>";
			
			break;
			
			case "pdf":
			
				echo "<object width='100%' height='100%' data='".$revision_file_data_uri."'></object>";
			
			break;
			
		};
		
	}
	
});

$app->get('/check/pickup_release/{id}', function ($request, $response, $args) {

	$id = $args['id'];
	
	session_start();
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];

	$con = $this->con;
	
	$sql = "SELECT * FROM tracks WHERE document_id = $id AND track_action_status IN ('picked up','released') AND office_id = $session_office";
	$picked_up_released = $con->getData($sql);
	
	$transit = array("pickup"=>false,"release"=>false);
	
	foreach ($picked_up_released as $pur) {
		
		if ($pur['track_action_status']=="picked up") {
			
			$transit['pickup'] = false;
			$transit['release'] = true;
			
		};
		
		if ($pur['track_action_status']=="released") {
			
			$transit['pickup'] = true;
			$transit['release'] = false;
			
		};		
		
	};
	
	return $response->withJson($transit);	

});

$app->run();

?>