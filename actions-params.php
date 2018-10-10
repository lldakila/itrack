<?php

define('actions_params', array(
	array(
		"id"=>1,
		"action_id"=>2,
		"description"=>"Travel Orders",
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
));

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