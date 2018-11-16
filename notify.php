<?php

function notify($con,$state,$params) {

	$old_table = $con->table;	
	$con->table = "notifications";

	$notifications = [];
	
	switch ($state) {

		case "added":
			
			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {
				
				$message = "Received at ".get_office_description($con,$params['initial_office'])." by ".get_staff_name($con,$params['recipient']);

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"user_id"=>$staff['id'],
					"icon"=>"icon-android-arrow-dropdown",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-primary",
					"header"=>$params['header'],
					"header_color"=>"",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
			};
		
		break;

		case "initialed":

			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {
				
				$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],
					"user_id"=>$staff['id'],
					"icon"=>"icon-android-checkmark-circle",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-primary",
					"header"=>$params['header'],
					"header_color"=>"",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
			};		
		
		break;
		
		case "approved":

			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {
				
				$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$staff['id'],
					"icon"=>"icon-checkmark",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-primary",
					"header"=>$params['header'],
					"header_color"=>"",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
			};	
		
		break;

		case "picked_up":
		
			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {

				# exclude if track_action_staff picked up the document
				if ($staff['id']==$params['track_action_staff']) continue;

				$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$staff['id'],
					"icon"=>"icon-briefcase",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-primary",
					"header"=>$params['header'],
					"header_color"=>"",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);
				
				$notifications[] = $notification;
				
			};		
		
		break;
		
		case "received":
		
			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {

				# exclude if track_action_staff picked up the document
				if ($staff['id']==$params['track_action_staff']) continue;
	
				$status = $params['track_action_status'];
				if ($params['file']) $status.=" and filed";
				$message = get_staff_name($con,$params['track_action_staff'])." $status the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$staff['id'],
					"icon"=>"icon-ios-location-outline",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-primary",
					"header"=>$params['header'],
					"header_color"=>"",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
			};		
		
		break;
		
		case "released":			
		
			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {

				# exclude if track_action_staff picked up the document
				if ($staff['id']==$params['track_action_staff']) continue;

				$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document to ".get_staff_name($con,$params['release_to']);

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$staff['id'],
					"icon"=>"icon-arrow44",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-primary",
					"header"=>$params['header'],
					"header_color"=>"",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	

				$notifications[] = $notification;

			};		

		break;

	};
	
	if (count($notifications)) $notify = $con->insertDataMulti($notifications);

	$con->table = $old_table;

};

function ago($d) {
	
	$now = date_create(date("Y-m-d H:i:s"));
	$date = date_create($d);

	$interval = date_diff($date,$now);

	$s = '';
	if (($interval->i)>1) $s = 's';
	$ago = $interval->format("%i minute$s ago");
	if (($interval->h)>0) {
		$s = '';
		if (($interval->h)>1) $s = 's';		
		$ago = $interval->format("%h hour$s ago");
	};
	if (($interval->d)>0) {
		$s = '';
		if (($interval->d)>1) $s = 's';
		$ago = $interval->format("%d day$s ago");
	};
	if (($interval->m)>0) {
		$s = '';
		if (($interval->m)>1) $s = 's';
		$ago = $interval->format("%m month$s ago");
	};	
	
	return $ago;
	
};

function get_staffs_by_group($con,$group,$office) {

	$staffs = $con->getData("SELECT id FROM users WHERE group_id = $group AND div_id = $office");

	return $staffs;

};

?>