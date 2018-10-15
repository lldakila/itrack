<?php

$actions_params = array(
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
			// array("id"=>2,"model"=>"remarks","description"=>"Remarks","type"=>"input","value"=>""),
		),
	),
);



$actions_params = array(
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
		),
	),
);

function get_params($actions_params,$id) {
	
	$params = [];

	foreach ($actions_params as $i => $dt) {

		if ($id == $dt['action_id']) {
			
			$params = $dt['params'];
			break;
			
		};
		
	};
		
	return $params;
	
};

?>