<?php

function get_files($dir,$barcode) {

	$files = [];

	if (!folder_exist($dir)) mkdir($dir);
	
	$dir_files = scandir($dir);

	foreach ($dir_files as $dir_file) {
		
		if ($dir_file == ".") continue;
		if ($dir_file == "..") continue;
		
		if (preg_match("/$barcode/",$dir_file)) {

			// Read image path, convert to base64 encoding
			$dir_file_data = base64_encode(file_get_contents($dir.$dir_file));

			// Format the image SRC:  data:{mime};base64,{data};
			$type = mime_content_type($dir.$dir_file);

			$file = 'data:'.$type.';base64,'.$dir_file_data;
			
			$file_type = explode("/",$type);

			$files[] = array(
				"file"=>$file,
				"type"=>$file_type[1],
				"name"=>$dir_file
			);

		};
		
	};

	return $files;

};

function get_action_track($tracks,$user_id,$office) { # user has action in track
	
	$track = array();

	foreach ($tracks as $t) {
		
		$param = get_track_action_param($t['track_action_add_params']);

		foreach ($param['options'] as $option) {
			
			if (($option['id'] == $user_id) && ($option['office']['id'] == $office) && ($option['value'])) {
				$track = $t;
				break 2;
			};

		};
		
	};
	
	return $track;

};
	

function get_track_action_param($param) { # convert to php array

	$param_arr = json_decode($param, true);

	return $param_arr;

};

function get_staff_action($track,$user_id,$office) {
	
	$action = array("action"=>null,"staff"=>null,"ok"=>false);	
	
	$param = get_track_action_param($track['track_action_add_params']);	
	
	foreach ($param['options'] as $option) {

		if (($option['id'] == $user_id) && ($option['office']['id'] == $office) && ($option['value'])) $action = array("action"=>$param['action_id'],"staff"=>$option,"ok"=>true);
		
	};
	
	return $action;
	
};

function user_has_action_doc($con,$track,$user_id) {
	
	$user_has_action_doc = array("track_id"=>null,"done"=>false);
	
	$param = get_track_action_param($track['track_action_add_params']);	
	
	$action_track = $con->getData("SELECT * FROM tracks WHERE track_action_staff = ".$user_id." AND preceding_track = ".$track['id']);

	if (count($action_track)) $user_has_action_doc = array("track_id"=>$action_track[0]['id'],"done"=>true);
	
	return $user_has_action_doc;
	
};

function get_staffs_actions($con,$track) {
	
	$staffs = [];
	
	$param = get_track_action_param($track['track_action_add_params']);
	
	foreach ($param['options'] as $option) {

		if ($option['value']) {
			
			$has_action = user_has_action_doc($con,$track,$option['id']);
			
			unset($option['value']);
			$option['done'] = $has_action['done'];
			$option['action_track_id'] = $has_action['track_id'];

			$staffs[] = $option;
			
		};
		
	};
	
	return $staffs;
	
};

?>