<?php

define('transit', array(
	array("id"=>1,"description"=>"At acting office"),
	array("id"=>2,"description"=>"picked up"),
	array("id"=>3,"description"=>"received"),
));

function transit_description($transit,$id) {
	
	$description = null;
	
	foreach ($transit as $t) {
		
		if ($id == $t['id']) $description = $t['description'];
		
	};
	
	return $description;
	
};

?>