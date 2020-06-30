<?php

define('document_actions', array(
	array("id"=>1,"key"=>"for_initial","description"=>"For Initial","done"=>"initialed"),
	array("id"=>2,"key"=>"for_signature","description"=>"For Signature","done"=>"approved"),
	array("id"=>3,"key"=>"for_routing","description"=>"Route/Refer","done"=>null),
	array("id"=>4,"key"=>"comment","description"=>"Comment","done"=>"commented"),
	array("id"=>5,"key"=>"revise","description"=>"Revise","done"=>"revise"),
	array("id"=>6,"key"=>"revised","description"=>"Revised","done"=>"revised"),
));

function document_action_done_status($document_actions,$id) {
	
	$status = null;
	
	foreach ($document_actions as $da) {
		
		if ($id == $da['id']) $status = $da['done'];
		
	};
	
	return $status;
	
};

?>