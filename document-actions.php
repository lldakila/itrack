<?php

define('document_actions', array(
	array("id"=>1,"key"=>"for_initial","description"=>"For Initial","done"=>"Initialed"),
	array("id"=>2,"key"=>"for_signature","description"=>"For Signature","done"=>"Approved"),
	array("id"=>3,"key"=>"for_routing","description"=>"Route/Refer","done"=>null),
));

function document_action_done_status($document_actions,$id) {
	
	$status = null;
	
	foreach ($document_actions as $da) {
		
		if ($id == $da['id']) $status = $da['done'];
		
	};
	
	return $status;
	
};

?>