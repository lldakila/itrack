<?php

define('transit', array(
	array("id"=>1,"description"=>"At acting office"),
	array("id"=>2,"description"=>"picked up"),
	array("id"=>3,"description"=>"received"),
	array("id"=>4,"description"=>"released"),
));

function transit_description($transit,$id) {
	
	$description = null;
	
	foreach ($transit as $t) {
		
		if ($id == $t['id']) $description = $t['description'];
		
	};
	
	return $description;
	
};

?>