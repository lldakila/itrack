<?php

define('dt_add_params', array(
	array(
		"id"=>1,
		"document_type"=>2,
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

function get_params($dt_add_params,$id) {
	
	$params = [];

	foreach ($dt_add_params as $i => $dt) {

		if ($id == $dt['document_type']) {
			
			$params = $dt['params'];
			break;
			
		};
		
	};
		
	return $params;
	
};

?>