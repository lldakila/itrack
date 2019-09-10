<?php

function get_files($dir,$barcode) {

	$files = [];

	// if (!folder_exist($dir)) mkdir($dir);
	
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

function get_document_files($con,$dir,$url,$id) {

	$base = str_replace("\\","/",__DIR__);
	// $dir = str_replace("/","\\",$dir);

	$files = [];

	// if (!folder_exist($dir)) mkdir($dir);
	
	$dir_files = $con->getData("SELECT * FROM files WHERE document_id = $id");

	foreach ($dir_files as $dir_file) {

		if (file_exists($base.$dir.$dir_file['file_name'])) {
			// Read image path, convert to base64 encoding

			$dir_file_data = base64_encode(file_get_contents($url.$dir_file['file_name']));

			// Format the image SRC:  data:{mime};base64,{data};
			$type = mime_content_type($url.$dir_file['file_name']);

			$file = 'data:'.$type.';base64,'.$dir_file_data;
			
			$file_type = explode("/",$type);

			$files[] = array(
				"id"=>$dir_file['id'],
				"file"=>$file,
				"type"=>$file_type[1],
				"name"=>$dir_file['file_name'],
				"initial_file"=>$dir_file['initial_file']
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

function get_office_description($con,$id) {
	
	$description = null;
	
	if ($id == null) return $description = "";
	
	$office = $con->getData("SELECT office FROM offices WHERE id = $id");
	
	if (count($office)) {
		
		$description = $office[0]['office'];
		
	};
	
	return $description;

};

function get_office_shortname($con,$id) {
	
	$shortname = null;
	
	if ($id == null) return $description = "";
	
	$office = $con->getData("SELECT shortname FROM offices WHERE id = $id");
	
	if (count($office)) {
		
		$shortname = $office[0]['shortname'];
		
	};
	
	return $shortname;

};

function get_staff_name($con,$id) {
	
	$fullname = null;
	
	$staff = $con->getData("SELECT CONCAT(IF(ISNULL(title),'',CONCAT(title,' ')), fname, ' ', IFNULL(SUBSTRING(mname,1,1),''), IF(ISNULL(mname),'','. '), lname) fullname FROM users WHERE id = $id");
	
	if (count($staff)) {
		
		$fullname = $staff[0]['fullname'];
		
	};
	
	return $fullname;
	
};

function get_action_staff_names($param) {
	
	$staffs = "";
	
	foreach ($param['options'] as $option) {
		
		if ($option['value']) {

			if ($staffs=="") $staffs.=$option['description'];
			else $staffs.=" and ".$option['description'];
		
		};
		
	};
	
	return $staffs;

};

function get_track_track_action($con,$track_id) {
	
	$track_action = null;
	
	$track = $con->getData("SELECT track_action FROM tracks WHERE id = $track_id");
	
	if (count($track)) $track_action = $track[0]['track_action'];
	
	return $track_action;
	
};

function get_transit_id($transit) {
	
	$transit_id = null;
	
	$_transit = json_decode($transit, true);
	$transit_id = $_transit['id'];
	
	return $transit_id;
	
};

function is_picked_up($transit) {
	
	$is_picked_up = false;
	
	$_transit = json_decode($transit, true);
	if ($_transit['picked_up_by']!=null) $is_picked_up = true; 
	
	return $is_picked_up;
	
};

function is_received($transit) {
	
	$is_received = false;
	
	$_transit = json_decode($transit, true);
	if ($_transit['received_by']!=null) $is_received = true; 
	
	return $is_received;	
	
};

function is_released($transit) {
	
	$is_released = false;
	
	$_transit = json_decode($transit, true);
	if ($_transit['id']==4) $is_released = true; 
	
	return $is_released;	
	
};

function is_received_filed($transit) {

	$is_received_filed = false;

	$_transit = json_decode($transit, true);
	if (($_transit['filed']) && ($_transit['received_by']!=null)) $is_received_filed = true;

	return $is_received_filed;	

};

function is_filed($transit) {

	$is_filed = false;

	$_transit = json_decode($transit, true);
	if ($_transit['filed']) $is_filed = true;

	return $is_filed;	

};

function get_transit_office($con,$transit) {
	
	$transit_office = null;
	
	$_transit = json_decode($transit, true);	
	
	$transit_office = get_office_description($con,$_transit['office']);
	
	return $transit_office;
	
};

function get_transit_release_to_office($con,$transit) {
	
	$transit_office = null;
	
	$_transit = json_decode($transit, true);	
	
	$transit_office = get_office_description($con,$_transit['release_to_office']);
	
	return $transit_office;
	
};

function get_transit_office_shortname($con,$transit) {
	
	$transit_office_shortname = null;
	
	$_transit = json_decode($transit, true);	
	
	$transit_office_shortname = get_office_shortname($con,$_transit['office']);
	
	return $transit_office_shortname;
	
};

function get_transit_office_id($con,$transit) {
	
	$transit_office_id = 0;
	
	$_transit = json_decode($transit, true);	
	
	$transit_office_id = $_transit['office'];
	
	return $transit_office_id;
	
};

function get_transit_staff($con,$transit,$p) {

	$transit_staff = null;
	
	$_transit = json_decode($transit, true);

	$id = $_transit[$p];

	$transit_staff = get_staff_name($con,$id);
	
	return $transit_staff;

};

function is_picked_up_by_other($transit) {
	
	$picked_up_by_other = false;
	
	$_transit = json_decode($transit, true);
	
	if (isset($_transit['picked_up_by_other'])) $picked_up_by_other = true;
	
	return $picked_up_by_other;
	
};

function get_transit_picked_up_other($transit) {
	
	$picked_up_by_other = "";
	
	$_transit = json_decode($transit, true);
	
	$picked_up_by_other = $_transit['picked_up_by_other'];
	
	return $picked_up_by_other;
	
};

function add_track($con,$data) {

	$table = $con->table;
	$con->table = "tracks";

	$insert_track = $con->insertData($data);

	$con->table = $table;

};

function is_office_admin($office) {
	
	$setup = new setup(system_setup);
	
	return $office == $setup->get_setup(13);
	
};

function is_office_opg($office) {

	
	$setup = new setup(system_setup);
	
	return $office == $setup->get_setup(12);	

	
};

function document_current_location($con,$id) {
	
	$current_location = "";
	
	$sql = "SELECT * FROM tracks WHERE document_id = $id ORDER BY id DESC LIMIT 1";
	
	$tracks = $con->getData($sql);
	
	if (count($tracks)) {
	
		$recent_track = $tracks[0];
		$current_location = get_transit_office_shortname($con,$recent_track['transit']);
		
	};
	
	return $current_location;
	
};

?>