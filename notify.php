<?php

function notify($con,$state,$params,$notify_group = true) {

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
					"icon_color"=>"bg-warning",
					"header"=>$params['header'],
					"header_color"=>"yellow darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
			};
		
		break;

		case "initialed":

			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";		
		
			if ($notify_group) {
		
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

				foreach ($staffs as $staff) {

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"track_id"=>$params['track_id'],
						"user_id"=>$staff['id'],
						"icon"=>"icon-android-checkmark-circle",
						"icon_bg"=>"icon-bg-circle",
						"icon_color"=>"bg-info",
						"header"=>$params['header'],
						"header_color"=>"cyan darken-3",
						"message"=>$message,
						"url"=>"/track-document.html#!/".$params['doc_id'],
					);	
					
					$notifications[] = $notification;
					
				};

			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-android-checkmark-circle",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-info",
					"header"=>$params['header'],
					"header_color"=>"cyan darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;				
				
			};
		
		break;
		
		case "approved":

			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";			
			
			if ($notify_group) {
			
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);			
				
				foreach ($staffs as $staff) {

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"track_id"=>$params['track_id'],					
						"user_id"=>$staff['id'],
						"icon"=>"icon-checkmark",
						"icon_bg"=>"icon-bg-circle",
						"icon_color"=>"bg-success",
						"header"=>$params['header'],
						"header_color"=>"green darken-3",
						"message"=>$message,
						"url"=>"/track-document.html#!/".$params['doc_id'],
					);	
					
					$notifications[] = $notification;
					
				};
				
			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-checkmark",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-success",
					"header"=>$params['header'],
					"header_color"=>"green darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;				
				
			};
		
		break;

		case "picked_up":
			
			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";			
			
			if ($notify_group) {
			
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

				foreach ($staffs as $staff) {

					# exclude if track_action_staff picked up the document
					if ($staff['id']==$params['track_action_staff']) continue;

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"track_id"=>$params['track_id'],					
						"user_id"=>$staff['id'],
						"icon"=>"icon-briefcase",
						"icon_bg"=>"icon-bg-circle",
						"icon_color"=>"bg-danger",
						"header"=>$params['header'],
						"header_color"=>"red darken-3",
						"message"=>$message,
						"url"=>"/track-document.html#!/".$params['doc_id'],
					);
					
					$notifications[] = $notification;
					
				};

			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-briefcase",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-danger",
					"header"=>$params['header'],
					"header_color"=>"red darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);
				
				$notifications[] = $notification;				
				
			};
		
		break;
		
		case "received":
		
			$status = $params['track_action_status'];
			if ($params['filed']) $status.=" and filed";
			$message = get_staff_name($con,$params['track_action_staff'])." $status the document";		

			if ($notify_group) {
			
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

				foreach ($staffs as $staff) {

					# exclude if track_action_staff picked up the document
					if ($staff['id']==$params['track_action_staff']) continue;

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"track_id"=>$params['track_id'],
						"user_id"=>$staff['id'],
						"icon"=>"icon-ios-location-outline",
						"icon_bg"=>"icon-bg-circle",
						"icon_color"=>"bg-danger",
						"header"=>$params['header'],
						"header_color"=>"red darken-3",
						"message"=>$message,
						"url"=>"/track-document.html#!/".$params['doc_id'],
					);

					$notifications[] = $notification;
					
				};

			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-ios-location-outline",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-danger",
					"header"=>$params['header'],
					"header_color"=>"red darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;				
				
			};
		
		break;
		
		case "released":			
		
			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document to ".get_staff_name($con,$params['release_to']);		

			if ($notify_group) {
			
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

				foreach ($staffs as $staff) {

					# exclude if track_action_staff released the document
					if ($staff['id']==$params['track_action_staff']) continue;

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"track_id"=>$params['track_id'],					
						"user_id"=>$staff['id'],
						"icon"=>"icon-arrow44",
						"icon_bg"=>"icon-bg-circle",
						"icon_color"=>"bg-danger",
						"header"=>$params['header'],
						"header_color"=>"red darken-3",
						"message"=>$message,
						"url"=>"/track-document.html#!/".$params['doc_id'],
					);	

					$notifications[] = $notification;

				};
			
			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-arrow44",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-danger",
					"header"=>$params['header'],
					"header_color"=>"red darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	

				$notifications[] = $notification;				
				
			};

		break;
		
		case "filed":
		
			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			var_dump($staffs);
			
			foreach ($staffs as $staff) {

				# exclude if track_action_staff filed the document
				if ($staff['id']==$params['track_action_staff']) continue;
	
				$status = $params['track_action_status'];
				if ($params['file']) $status.=" and filed";
				$message = get_staff_name($con,$params['track_action_staff'])." $status the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$staff['id'],
					"icon"=>"icon-android-folder-open",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-danger",
					"header"=>$params['header'],
					"header_color"=>"red darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
			};		
		
		break;

		case "commented":
		
			$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

			foreach ($staffs as $staff) {

				$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." on the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$staff['id'],
					"icon"=>"icon-pencil22",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-info",
					"header"=>$params['header'],
					"header_color"=>"cyan darken-3",
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

	$staffs = $con->getData("SELECT id FROM users WHERE group_id IN ($group) AND div_id = $office");

	return $staffs;

};

function get_admin_recipient($con,$id) {

	$document = $con->getData("SELECT user_id FROM documents WHERE id = $id");

	return $document[0]['user_id'];

}

?>