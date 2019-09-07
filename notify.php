<?php

function notify($con,$state,$params,$notify_group = true) {

	$old_table = $con->table;	
	$con->table = "notifications";

	$notifications = [];
	$emails = [];
	
	switch ($state) {

		case "added":
			
			if ($notify_group) {
			
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

					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;

				};
				
			} else {
				
				var_dump("notified");
				
				$message = "Received at ".get_office_description($con,$params['initial_office'])." by ".get_staff_name($con,$params['recipient']);

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-android-arrow-dropdown",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-warning",
					"header"=>$params['header'],
					"header_color"=>"yellow darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;

				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;				
				
			};
		
		break;

		case "initialed":

			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";		
		
			$pa_staffs = get_group_staffs($con,$params['pa_staffs']);
			$inform_seen = $pa_staffs;
			
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			};
			
			$opg_office = $params['setup']->get_setup(12);
			if ($_SESSION['office'] == $opg_office) {
				
				$opg_staffs = get_staffs_by_group($con,$params['group'],$opg_office);
				$inform_seen = array_merge($pa_staffs,$opg_staffs);
				
			};

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
						"inform_seen"=>$inform_seen,
					);	
					
					$notifications[] = $notification;
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					
					
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
					"inform_seen"=>$inform_seen,					
				);	
				
				$notifications[] = $notification;
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
			};
		
		break;
		
		case "approved":

			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document";			
			
			$pa_staffs = get_group_staffs($con,$params['pa_staffs']);
			$inform_seen = $pa_staffs;
			
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			};
			
			$opg_office = $params['setup']->get_setup(12);
			if ($_SESSION['office'] == $opg_office) {
				
				$inform_seen = json_decode($inform_seen,true);
				
				$opg_staffs = get_staffs_by_group($con,$params['group'],$opg_office);
				foreach ($opg_staffs as $opg_staff) {
					
					$inform_seen[] = $opg_staff['id'];
					
				};
				
				$inform_seen = json_encode($inform_seen);
				
			};

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
						"inform_seen"=>$inform_seen,						
					);	
					
					$notifications[] = $notification;
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					
					
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
					"inform_seen"=>$inform_seen,					
				);	
				
				$notifications[] = $notification;
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
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
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					
					
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
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
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
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					
					
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
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
			};
		
		break;
		
		case "released":			
		
			if ($params['track_action_staff']>0) $message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." the document to ".get_office_description($con,$params['release_to']);		
			else $message = "The document was ".$params['track_action_status']." to ".get_office_description($con,$params['release_to']);		

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
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					

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
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
			};

		break;
		
		case "filed":

			if ($notify_group) {
		
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);
				
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
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;				
					
				};
				
			} else {

				$status = $params['track_action_status'];
				if ($params['file']) $status.=" and filed";
				$message = get_staff_name($con,$params['track_action_staff'])." $status the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-android-folder-open",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-danger",
					"header"=>$params['header'],
					"header_color"=>"red darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],					
				);	
				
				$notifications[] = $notification;
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;				
			
			}
		
		break;

		case "commented":

			if ($notify_group) {
		
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
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;	
					
				};
				
			} else {
				
				$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status']." on the document";

				$notification = array(
					"doc_id"=>$params['doc_id'],
					"track_id"=>$params['track_id'],					
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-pencil22",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-info",
					"header"=>$params['header'],
					"header_color"=>"cyan darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);
				
				$notifications[] = $notification;
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
			};
		
		break;
		
		case "add_revision":
		
			$message = get_staff_name($con,$params['track_action_staff'])." ".$params['track_action_status'];
		
			if ($notify_group) {
		
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

				foreach ($staffs as $staff) {

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"revision_id"=>$params['revision_id'],
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
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					
					
				};

			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"revision_id"=>$params['revision_id'],
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-pencil22",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-info",
					"header"=>$params['header'],
					"header_color"=>"cyan darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
			};		
		
		break;
		
		case "revision_ok":
		
			$message = $params['track_action_status'];
		
			if ($notify_group) {
		
				$staffs = get_staffs_by_group($con,$params['group'],$params['office']);

				foreach ($staffs as $staff) {

					$notification = array(
						"doc_id"=>$params['doc_id'],
						"revision_id"=>$params['revision_id'],
						"user_id"=>$staff['id'],
						"icon"=>"icon-checkbox-checked",
						"icon_bg"=>"icon-bg-circle",
						"icon_color"=>"bg-info",
						"header"=>$params['header'],
						"header_color"=>"cyan darken-3",
						"message"=>$message,
						"url"=>"/track-document.html#!/".$params['doc_id'],
					);	
					
					$notifications[] = $notification;
					
					$email = array(
						"user"=>get_staff_info($con,$staff['id']),
						"subject"=>$params['header'],
						"message"=>$message
					);

					$emails[] = $email;					
					
				};

			} else {
				
				$notification = array(
					"doc_id"=>$params['doc_id'],
					"revision_id"=>$params['revision_id'],
					"user_id"=>$params['notify_user'],
					"icon"=>"icon-checkbox-checked",
					"icon_bg"=>"icon-bg-circle",
					"icon_color"=>"bg-info",
					"header"=>$params['header'],
					"header_color"=>"cyan darken-3",
					"message"=>$message,
					"url"=>"/track-document.html#!/".$params['doc_id'],
				);	
				
				$notifications[] = $notification;
				
				$email = array(
					"user"=>get_staff_info($con,$params['notify_user']),
					"subject"=>$params['header'],
					"message"=>$message
				);

				$emails[] = $email;					
				
			};		
		
		break;

	};

	if (count($notifications)) {

		$notify = $con->insertDataMulti($notifications);
		
		// email_notification($emails);

	};

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

function email_notification($emails) {
	
	foreach ($emails as $email) {
	
		$data = $email;
		$url = "http://".$_SERVER['HTTP_HOST']."/email_notification.php";

		$options = array(
			'http'=>array(
				'header'=>"Content-type: application/x-www-form-urlencoded\r\n",
				'method'=>'POST',
				'content'=>http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$message = file_get_contents($url, false, $context);	

		$address = "sly@christian.com.ph";
		$subject = $data['subject'];

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: iTrack (Document Tracking System) <sly14flores@gmail.com>' . "\r\n";

		$send = mail($address,$subject,$message,$headers);
		
	};

};

function get_staffs_by_group($con,$group,$office) {

	$staffs = $con->getData("SELECT id FROM users WHERE group_id IN ($group) AND div_id = $office");

	return $staffs;

};

function get_staffs_by_group_only($con,$group) {

	$staffs = $con->getData("SELECT id FROM users WHERE group_id IN ($group)");

	return $staffs;

};

function get_group_staffs($con,$group) {

	$staffs_arr = [];

	$staffs = $con->getData("SELECT id FROM users WHERE group_id IN ($group)");

	foreach ($staffs as $staff) {

		$staffs_arr[] = $staff['id'];

	};

	return json_encode($staffs_arr);

};

function get_admin_recipient($con,$id) {

	$document = $con->getData("SELECT user_id FROM documents WHERE id = $id");

	return $document[0]['user_id'];

};

function get_staff_info($con,$id) {
	
	$user = $con->getData("SELECT id, fname, mname, lname, email_address FROM users WHERE id = $id");

	return $user[0];
	
};

?>