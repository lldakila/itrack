<?php

define('assignments', array(
	"user"=>array(
		array("id"=>1,"user"=>[6],"description"=>"Provincial Administrator"),
		array("id"=>2,"user"=>[12],"description"=>"Assistant Provincial Administrator"),
	),
	"office"=>array(
		array("id"=>1,"office"=>[2],"description"=>"Initial recepient of documents"),	
	),
));

function getAssignmentIds($assignments,$id,$col) {
	
	$value = NULL;
	
	foreach ($assignments as $assignment) {
		
		if ($id == $assignment['id']) $value = "(".implode(",",$assignment[$col]).")";
		
	};
	
	return $value;
	
};

function getAssignmentId($assignments,$id,$col) {
	
	$value = NULL;
	
	foreach ($assignments as $assignment) {
		
		if ($id == $assignment['id']) $value = $assignment[$col][0];
		
	};
	
	return $value;
	
};

?>