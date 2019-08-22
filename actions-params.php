<?php

/* $actions_params = array(
	array(
		"id"=>1,
		"action_id"=>1,
		"description"=>"For Initial",
		"params"=>array(
			array("id"=>1,"model"=>"destination","description"=>"Destination","type"=>"select","value"=>array("id"=>0,"description"=>"-"),"options"=>array(
					array("id"=>0,"description"=>"-"),
					array("id"=>1,"description"=>"External"),
					array("id"=>2,"description"=>"Internal"),
				)
			),
			array("id"=>2,"model"=>"remarks","description"=>"Remarks","type"=>"input","value"=>""),
		),
	),
); */

require_once 'system_setup.php';

$system_setup = new setup(system_setup);

if (!isset($_SESSION)) session_start();

$users_for_initial_selects = [];
$users_ids_initial = $system_setup->get_setup(1); # users for document action: initial
$initial_users = (isset($users_ids_initial[$_SESSION['office']]))?implode(",",$users_ids_initial[$_SESSION['office']]['values']):"0";
$users_for_initial = $con->getData("SELECT id, CONCAT(IF(ISNULL(title),'',CONCAT(title,' ')), fname, ' ', IFNULL(SUBSTRING(mname,1,1),''), IF(ISNULL(mname),'','. '), lname) description FROM users WHERE id IN ($initial_users)");

// $users_for_initial_selects[] = array("id"=>0,"description"=>"-","office"=>array("id"=>0,"office"=>"-"),"value"=>false);
foreach ($users_for_initial as $value) {
	
	$office = $con->getData("SELECT users.div_id id, (SELECT office FROM offices WHERE offices.id = users.div_id) office FROM users WHERE users.id = ".$value['id']);
	
	$value['office'] = $office[0];
	$value['value'] = false;	
	
	$users_for_initial_selects[] = $value;
	
};

$users_for_signature_selects = [];
$users_ids_signature = $system_setup->get_setup(2); # users for document action: signature
$approve_users = (isset($users_ids_signature[$_SESSION['office']]))?implode(",",$users_ids_signature[$_SESSION['office']]['values']):"0";
$users_for_signature = $con->getData("SELECT id, CONCAT(IF(ISNULL(title),'',CONCAT(title,' ')), fname, ' ', IFNULL(SUBSTRING(mname,1,1),''), IF(ISNULL(mname),'','. '), lname) description FROM users WHERE id IN ($approve_users)");

// $users_for_signature_selects[] = array("id"=>0,"description"=>"-","office"=>array("id"=>0,"office"=>"-"),"value"=>false);
foreach ($users_for_signature as $value) {
	
	$office = $con->getData("SELECT users.div_id id, (SELECT office FROM offices WHERE offices.id = users.div_id) office FROM users WHERE users.id = ".$value['id']);
	
	$value['office'] = $office[0];
	$value['value'] = false;	
	
	$users_for_signature_selects[] = $value;
	
};


$users_ids_route = $system_setup->get_setup_as_string(3); # users for document action: route
$users_for_route = $con->getData("SELECT id, CONCAT(IF(ISNULL(title),'',CONCAT(title,' ')), fname, ' ', IFNULL(SUBSTRING(mname,1,1),''), IF(ISNULL(mname),'','. '), lname) description FROM users WHERE id IN ($users_ids_route)");

$users_for_route_selects[] = array("id"=>0,"description"=>"-","office"=>array("id"=>0,"office"=>"-"));
foreach ($users_for_route as $value) {
	
	$office = $con->getData("SELECT users.div_id id, (SELECT office FROM offices WHERE offices.id = users.div_id) office FROM users WHERE users.id = ".$value['id']);
	
	$value['office'] = $office[0];
	
	$users_for_route_selects[] = $value;
	
};

$actions_params = array(
	array(
		"id"=>1,
		"description"=>"For Initial",
		"params"=>array(
			array(
				"id"=>1,
				"action_id"=>1,
				"model"=>"action_user_id",
				"description"=>"To",
				"type"=>"checkbox",
				"options"=>$users_for_initial_selects,			
			),
		),
	),
	array(
		"id"=>2,
		"description"=>"For Signature",
		"params"=>array(
			array(
				"id"=>1,
				"action_id"=>2,				
				"model"=>"action_user_id",
				"description"=>"To",
				"type"=>"checkbox",
				"options"=>$users_for_signature_selects,			
			),
		),
	),
	/* array(
		"id"=>3,
		"description"=>"For Route",
		"params"=>array(
			array(
				"id"=>1,
				"action_id"=>3,				
				"model"=>"action_user_id",
				"description"=>"To",
				"type"=>"select",
				"value"=>array("id"=>0,"description"=>"-","office"=>array("id"=>0,"office"=>"-")),		
				"options"=>$users_for_route_selects,			
			),
		),
	), */
);

function get_params($actions_params,$id) {

	$params = [];

	foreach ($actions_params as $i => $dt) {

		if ($id == $dt['id']) {
			
			$params = $dt['params'];
			break;
			
		};

	};

	return $params;

};

?>