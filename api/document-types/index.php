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

# list document types
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$doc_types = $con->getData("SELECT * FROM document_types");	
	
    return $response->withJson($doc_types);

});

# add document type
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";
	
	$data = $request->getParsedBody();
	
	$staff_assigns = $data['staff_assign'];
	unset($data['staff_assign']);

	$staff_assign_dels = $data['staff_assign_dels'];
	unset($data['staff_assign_dels']);	

	unset($data['id']);
	$con->insertObj($data);
	$id = $con->insertId;

	if (count($staff_assigns)) {

		$con->table = "document_types_staffs";

		foreach ($staff_assigns as $index => $value) {
			
			$staff_assigns[$index]['document_type'] = $id;			
			$staff_assigns[$index]['staff_id'] = $value['staff_id']['id'];
			unset($staff_assigns[$index]['fullname']);

		};

		foreach ($staff_assigns as $index => $value) {
				
			unset($value['id']);
			$staff_row = $con->insertData($value);
		
		};
		
	};

});

# update document type
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;

	$data = $request->getParsedBody();
	
	$staff_assigns = $data['staff_assign'];
	unset($data['staff_assign']);

	$staff_assign_dels = $data['staff_assign_dels'];
	unset($data['staff_assign_dels']);
	
	# staff info
	if (count($staff_assign_dels)) {

		$con->table = "document_types_staffs";
		$delete = $con->deleteData(array("id"=>implode(",",$staff_assign_dels)));		
			
	};

	if (count($staff_assigns)) {

		$con->table = "document_types_staffs";

		foreach ($staff_assigns as $index => $value) {
			
			unset($value['fullname']);			
			
			if ($value['id']) {
				
				$value['staff_id'] = $value['staff_id']['id'];
				$staff_row = $con->updateData($value,'id');
				
			} else {
				
				unset($staff_assigns[$index]['id']);
				$value['staff_id'] = $value['staff_id']['id'];
				$value['document_type'] = $data['id'];
				$staff_row = $con->insertData($value);
				
			}
		
		}
		
	};
	
	$con->table = "document_types";	
	$con->updateObj($data,'id');

});

# view document type
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";

	$doc_type = $con->get(array("id"=>$args['id']));
	
	foreach($doc_type as $key => $dt){
		
		$staff_assign = $con->getData("SELECT * FROM document_types_staffs WHERE document_type = ".$dt['id']);
		
			foreach($staff_assign as $i => $staff){
				
				$staffs = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$staff['staff_id']);	
				$staff_assign[$i]['staff_id'] = $staffs[0];
			}
		
		$doc_type[$key]['staff_assign'] = $staff_assign;
	} 
	
	$doc_type[0]['staff_assign_dels'] = [];
	
    return $response->withJson($doc_type[0]);

});


# delete document type
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "document_types";
	
	$doc_type = array("id"=>$args['id']);

	$con->deleteData($doc_type);

});

$app->get('/staffs', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "users";	

	require_once '../../system_setup.php';	
	$system_setup = system_setup;
	$setup = new setup($system_setup);
	
	$in = $setup->get_values_as_string(1);
	
	$staffs = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE group_id IN ($in)");	

    return $response->withJson($staffs);

});

$app->run();

?>