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

$users_ids_initial_signature = $system_setup->get_setup_as_string(1); # users for document action: initial/signature
$users_for_initial_signature = $con->getData("SELECT id, CONCAT_WS(' ',fname, lname) description FROM users WHERE id IN ($users_ids_initial_signature)");

$users_for_initial_signature_selects[] = array("id"=>0,"description"=>"-");
foreach ($users_for_initial_signature as $value) {
	
	$users_for_initial_signature_selects[] = $value;
	
};

$users_ids_route = $system_setup->get_setup_as_string(2); # users for document action: route
$users_for_route = $con->getData("SELECT id, CONCAT_WS(' ',fname, lname) description FROM users WHERE id IN ($users_ids_route)");

$users_for_route_selects[] = array("id"=>0,"description"=>"-");
foreach ($users_for_route as $value) {
	
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
				"type"=>"select",
				"value"=>array("id"=>0,"description"=>"-"),
				"options"=>$users_for_initial_signature_selects,			
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
				"type"=>"select",
				"value"=>array("id"=>0,"description"=>"-"),
				"options"=>$users_for_initial_signature_selects,			
			),
		),
	),
	array(
		"id"=>3,
		"description"=>"For Route",
		"params"=>array(
			array(
				"id"=>1,
				"action_id"=>3,				
				"model"=>"action_user_id",
				"description"=>"To",
				"type"=>"select",
				"value"=>array("id"=>0,"description"=>"-"),
				"options"=>$users_for_route_selects,			
			),
		),
	),
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