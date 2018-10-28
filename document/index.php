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
	require_once '../functions.php';
	
	$id = $args['id'];
	
	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params FROM documents WHERE id = $id");
	
	if (count($document)) {
		
		# first track
		$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = ".$document[0]['id']." ORDER BY system_log LIMIT 1");
		
		$document[0]['for_initial'] = false;
		$document[0]['for_signature'] = false;
		$document[0]['for_routing'] = false;
		
		$document[0]['dt_add_params'] = json_decode($document[0]['dt_add_params'],false);
		$document[0]['document_dt_add_params'] = $document[0]['dt_add_params'];
		$document[0]['document_action_add_params'] = [];
		
		if (count($tracks)) {
			
			$first_track = $tracks[0];
			
			if ($first_track['track_action']==1) $document[0]['for_initial'] = true;
			if ($first_track['track_action']==2) $document[0]['for_signature'] = true;
			if ($first_track['track_action']==3) $document[0]['for_routing'] = true;
			
			$document[0]['document_action_add_params'] = json_decode($first_track['track_action_add_params'],false);
			
		};
		
		$document[0]['document_date_barcode'] = date("M j, Y h:i:s A",strtotime($document[0]['document_date']));
		
		$document[0]['files'] = [];
		$document[0]['delete_files'] = [];

		$files = get_files("../files/",$document[0]['barcode']);
		$document[0]['files'] = $files;

		$document = document_info($con,$document[0]);
		
	};
	
	return $response->withJson($document);

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

	# document_action_add_params
	$document_action_add_params = $data['document_action_add_params'];
	unset($data['document_action_add_params']);
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
	
	$track_action = 0;
	
	if ($data['for_initial']) $track_action = 1;
	if ($data['for_signature']) $track_action = 2;
	if ($data['for_routing']) $track_action = 3;

	unset($data['for_initial']);
	unset($data['for_signature']);
	unset($data['for_routing']);

	$uploads = array("files"=>$data['files']);	
	unset($data['files']);

	$data['dt_add_params'] = json_encode($document_dt_add_params);

	unset($data['document_date_barcode']);
	
	$data['update_log'] = "CURRENT_TIMESTAMP";
	$con->updateData($data,'id');
	
	# first track
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY system_log LIMIT 1");	
	
	if (count($tracks)) {

		$con->table = "tracks";	
	
		$first_track = $tracks[0];
		$track = array(
			"id"=>$first_track['id'],
			"track_action"=>$track_action,
			"track_action_add_params"=>json_encode($document_action_add_params),
			"track_action_status"=>null,
			"track_user"=>$_SESSION['itrack_user_id'],
			"update_log"=>"CURRENT_TIMESTAMP"
		);

		$con->updateData($track,'id');

	};
	#

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
	
	# tracks
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY system_log");
	$first_track = $tracks[0]; # first track
	
	$param = get_track_action_param($first_track['track_action_add_params']);

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];	
	
	$action = get_staff_action($param,$session_user_id,$session_office);	
	
    return $this->view->render($response, 'initial.html', [
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

	# first track
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY system_log LIMIT 1");	
	$first_track = $tracks[0];
	
	$param = get_track_action_param($first_track['track_action_add_params']);
	
	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];

	$action = get_staff_action($param,$session_user_id,$session_office);

	return $response->withJson($action);

});

$app->get('/for/initial/doc/{id}', function ($request, $response, $args) {

	$con = $this->con;
	$con->table = "documents";	

	require_once '../functions.php';
	
	$id = $args['id'];

	$document = $con->getData("SELECT id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date, dt_add_params FROM documents WHERE id = $id");

	# first track
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY system_log LIMIT 1");	
	$first_track = $tracks[0];	
	
	$files = get_files("../files/",$document[0]['barcode']);

	return $response->withJson(array("files"=>$files,"first_track"=>$first_track));

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
		"preceding_track"=>$data['first_track']['id'],
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

$app->run();

?>