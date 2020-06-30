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
$app->post('/list/{entryLimit}/{currentPage}', function (Request $request, Response $response, array $args) {

	$currentPage = $args['currentPage'];
	$entryLimit = $args['entryLimit'];

	$con = $this->con;
	$con->table = "documents";
	
	require_once '../../functions.php';
	require_once '../../document-info.php';
	require_once '../../system_setup.php';
	require_once '../../tracks.php';
	
	$system_setup = system_setup;
	$setup = new setup($system_setup);	

	$init = ((intval($entryLimit)==0) && (intval($currentPage)==1));

	$offset = ($currentPage-1)*$entryLimit;
	$limit = " LIMIT $offset, $entryLimit";

	if ($init) $limit = "";

	$data = $request->getParsedBody();
	
	$criteria = ["origin","communication","document_transaction_type","doc_type","barcode"];
	
	$filters = "";
	foreach ($criteria as $i => $criterion) {
		
		if (isset($data[$criterion]['id'])) {
			if ($data[$criterion]['id']==0) continue;			
			if ($filters=="") $filters.=" WHERE $criterion = ".$data[$criterion]['id'];
			else $filters.=" AND $criterion = ".$data[$criterion]['id'];
		} else {
			if (isset($data[$criterion])) {
				
				if ($criterion=="barcode") {
					if ($data[$criterion]=="") {
						unset($data[$criterion]);
						continue;
					}
				}				
				
				if ($filters=="") $filters.=" WHERE $criterion = '".$data[$criterion]."'";
				else $filters.=" AND $criterion = '".$data[$criterion]."'";
			};
		};

	};

	$sql = "SELECT id, user_id, barcode, doc_name, doc_type, origin, other_origin, communication, document_transaction_type, document_date FROM documents".$filters.$limit;
	$documents = $con->getData($sql);

	if ($init) return $response->withJson(array("count"=>count($documents)));

	foreach ($documents as $i => $document) {
		
		$tracks = tracks($con,$setup,$document['id'],$document);
		
		$documents[$i] = document_info_complete($con,$document);
		
		$recent_status = ($tracks[0]['list'][0]['status']['comment']==null)?$tracks[0]['list'][0]['status']['text']:$tracks[0]['list'][0]['status']['comment'];
		$documents[$i]['recent_status'] = $recent_status;
		
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

	require_once '../folder-files.php';

	$con = $this->con;
	$con->table = "documents";
	
	$id = $args['id'];
	$document = array("id"=>$id);

	$files = $con->getData("SELECT file_name FROM files WHERE document_id = ".$args['id']);	
	
	$files_dir = "../../files/";
	foreach ($files as $file) {
		
		if (file_exists($files_dir.$file['file_name'])) unlink($files_dir.$file['file_name']);
		
	};
	
	# delete revisions files
	$revisions_dir = "../../revisions/$id";
	if (folder_exist($revisions_dir)) {
		$revisions = scandir($revisions_dir);
		
		foreach ($revisions as $revision) {
			
			if ($revision==".") continue;
			if ($revision=="..") continue;
			
			if (file_exists($revisions_dir."/$revision")) unlink($revisions_dir."/$revision");
			
		};
	};
	
	# delete folder-files
	if (folder_exist($revisions_dir)) rmdir($revisions_dir);
	
	$con->deleteData($document);

});

$app->run();

?>