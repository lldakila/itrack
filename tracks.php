<?php

function tracks($con,$setup,$id,$document) {

	$document_tracks =[];

	$initial_list = [];
	$for_initial = [];
	$for_signature = [];	
	
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY system_log DESC");
	
	foreach ($tracks as $track) {

		$list = [];
		$icon = "icon-ios-location-outline";
		
		# for initial / signature	
		if ($track['track_action'] == 1) {
			$for_initial = array(
				"status"=>"For initial for ".get_action_staff_names(get_track_action_param($track['track_action_add_params']))
			);
		};
		
		if ($track['track_action'] == 2) {
			$for_signature = array(
				"status"=>"For signature for ".get_action_staff_names(get_track_action_param($track['track_action_add_params']))
			);			
		};		
		
		# comment
		if ($track['track_action'] == 4) {
			
			$status = get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." on the document";
			$status .= '<blockquote class="blockquote">';
			$status .= '<p class="mb-0">'.$track['comment'].'</p>';
			$status .= '</blockquote>';
			
			$list[] = array(
				"status"=>$status,
			);			
			
			$bg = "bg-info";
			
			$document_tracks[] = array(
				"icon"=>"icon-pencil22",
				"bg"=>$bg,
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);
			
		};
		
		# initialed / approved
		if ($track['preceding_track']!=null) {
			
			$ia_icons = array(null,"icon-android-checkmark-circle","icon-checkmark");
			
			$list[] = array(
				"status"=>get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." the document"
			);
			
			$bg = "bg-info";
			
			if ($track['preceding_track']==2) $bg = "bg-success";
			
			$document_tracks[] = array(
				"icon"=>$ia_icons[get_track_track_action($con,$track['preceding_track'])],
				"bg"=>$bg,
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);			
			
		};
		
		# picked up / received
		$t_icons = array(null,"icon-android-arrow-dropdown","icon-briefcase","icon-ios-location-outline","icon-arrow44","icon-android-folder-open");		
		
		if (is_picked_up($track['transit'])) {
			
			$status = $track['track_action_status'];
			
			$list[] = array(
				"status"=>get_staff_name($con,$track['track_action_staff'])." $status the document"
			);
			
			$document_tracks[] = array(
				"icon"=>$t_icons[get_transit_id($track['transit'])],
				"bg"=>"bg-danger",
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);
			
		};
		
		if (is_received($track['transit'])) {

			$status = $track['track_action_status'];
			if (is_received_filed($track['transit'])) $status.=" and filed";			

			$list[] = array(
				"status"=>get_staff_name($con,$track['track_action_staff'])." $status the document"
			);

			$document_tracks[] = array(
				"icon"=>$t_icons[get_transit_id($track['transit'])],
				"bg"=>"bg-danger",
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);

		};

		if (is_released($track['transit'])) {
			
			$list[] = array(
				"status"=>get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." the document to ".get_transit_staff($con,$track['transit'],"released_to")
			);
			
			$document_tracks[] = array(
				"icon"=>$t_icons[get_transit_id($track['transit'])],
				"bg"=>"bg-danger",
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);
			
		};
		
		if (is_filed($track['transit'])) {

			$status = $track['track_action_status'];		

			$list[] = array(
				"status"=>get_staff_name($con,$track['track_action_staff'])." $status the document"
			);

			$document_tracks[] = array(
				"icon"=>$t_icons[get_transit_id($track['transit'])],
				"bg"=>"bg-danger",
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);

		};		

	};
	
	# first track

	$initial_office = $setup->get_setup_as_string(4);

	$initial_list[] = array("status"=>"Received at ".get_office_description($con,$initial_office)." by ".get_staff_name($con,$document['user_id']));	
	if (count($for_initial)) $initial_list[] = $for_initial;
	if (count($for_signature)) $initial_list[] = $for_signature;	

	$document_tracks[] = array(
		"icon"=>"icon-android-arrow-dropdown",
		"bg"=>"bg-warning",
		"track_time"=>date("h:i:s A",strtotime($document['document_date'])),
		"track_date"=>date("M j, Y",strtotime($document['document_date'])),
		"list"=>$initial_list,
	);

	return $document_tracks;

};

?>