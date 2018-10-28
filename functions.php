<?php

function get_files($dir,$barcode) {

	$files = [];

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

function get_track_action_param($param) {

	$param_arr = json_decode($param, true);

	return $param_arr;

};

function get_staff_action($param,$user_id,$office) {
	
	$check = array("staff"=>$session_user_id==$param_user_id,"office"=>$session_office==$param_office);
	
	$action = array("action"=>null,"staff"=>null,"ok"=>false);	
	
	foreach ($param as $p) {
		
		if (($p['id'] == $user_id) && ($p['office']['id'] == $office)) 
		
	};
	
};

?>