<?php

function tracks($con,$setup,$id,$document) {

	$initial_office = $setup->get_setup_as_string(4);

	$document_tracks =[];

	$initial_list = [];
	$for_initial = [];
	$for_signature = [];	

	$received_next_office = [];

	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = $id ORDER BY id DESC");

	foreach ($tracks as $i => $track) {

		$transit_office = get_transit_office_id($con,$track['transit']);		

		$list = [];
		$icon = "icon-ios-location-outline";
						
		# for initial / signature	
		if ($track['track_action'] == 1) {			
			if ($transit_office==$initial_office) { # OPA
				$for_initial = array(
					"status"=>array(
						"text"=>"For initial for ".get_action_staff_names(get_track_action_param($track['track_action_add_params'])),
						"comment"=>null,
					)
				);
			} else {
				$received_next_office[] = array(
					"status"=>array(
						"text"=>"For initial for ".get_action_staff_names(get_track_action_param($track['track_action_add_params'])),
						"comment"=>null,
					)				
				);
			}
		};

		if ($track['track_action'] == 2) {			
			if ($transit_office==$initial_office) { # OPA
				$for_signature = array(
					"status"=>array(
						"text"=>"For signature for ".get_action_staff_names(get_track_action_param($track['track_action_add_params'])),
						"comment"=>null,
					)
				);
			} else {
				$received_next_office[] = array(
					"status"=>array(
						"text"=>"For signature for ".get_action_staff_names(get_track_action_param($track['track_action_add_params'])),
						"comment"=>null,
					)
				);				
			}
		};				
		
		# comment
		if ($track['track_action'] == 4) {
			
			$status = get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." on the document";
			$status .= '<blockquote class="blockquote">';
			$status .= '<p class="mb-0">'.$track['comment'].'</p>';
			$status .= '</blockquote>';
			
			$list[] = array(
				"status"=>array(
					"text"=>$status,
					"comment"=>get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." on the document",
				)
			);			
			
			$bg = "bg-info";
			
			$document_tracks[] = array(
				"track_id"=>$track['id'],
				"id"=>$track['track_action'],
				"icon"=>"icon-pencil22",
				"bg"=>$bg,
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);
			
		};
		
		# revise
		if ($track['track_action'] == 5) {

			$revision = $con->getData("SELECT * FROM revisions WHERE id = ".$track['revision_id']);
			$uploaded_file = $revision[0]['uploaded_file'];			
			
			$status = get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." on the document";
			$status .= '<blockquote class="blockquote">';
			// $status .= '<p class="mb-0 ws">'.$uploaded_file.'</p>';
			$status .= '<a href="/document/doc/revisions/preview/'.$id.'/'.$uploaded_file.'" target="_blank">'.$uploaded_file.'</a>';
			$status .= '</blockquote>';

			$list[] = array(
				"status"=>array(
					"text"=>$status,
					"comment"=>get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." on the document",
				)
			);			
			
			$bg = "bg-info";
			
			$document_tracks[] = array(
				"track_id"=>$track['id'],			
				"id"=>$track['track_action'],
				"icon"=>"icon-pencil22",
				"bg"=>$bg,
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);		

		};
		
		# revised / revision ok
		if ($track['track_action'] == 6) {

			$revision = $con->getData("SELECT * FROM revisions WHERE id = ".$track['revision_id']);
			$revision_date = date("F j, Y h:i A",strtotime($revision[0]['system_log']));
			$revision_ok_date = date("F j, Y h:i A",strtotime($revision[0]['datetime_completed']));
		
			$status = "Revisions added on $revision_date are marked ok";
			
			$list[] = array(
				"status"=>array(
					"text"=>$status,
					"comment"=>null
				)
			);			
			
			$bg = "bg-info";
			
			$document_tracks[] = array(
				"track_id"=>$track['id'],			
				"id"=>$track['track_action'],
				"icon"=>"icon-checkbox-checked",
				"bg"=>$bg,
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);		

		};		
		
		# initialed / approved
		if (($track['track_action_status']=="initialed") || ($track['track_action_status']=="approved")) {
			
			$ia_icons = array(null,"icon-android-checkmark-circle","icon-checkmark");
			
			$list[] = array(
				"status"=>array(
					"text"=>get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." the document",
					"comment"=>null,
				)
			);
			
			$bg = "bg-info";
			$track_action = get_track_track_action($con,$track['preceding_track']);
			if ($track_action==2) $bg = "bg-success";
			
			$document_tracks[] = array(
				"track_id"=>$track['id'],			
				"id"=>$track['track_action'],			
				"icon"=>$ia_icons[$track_action],
				"bg"=>$bg,
				"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
				"track_date"=>date("M j, Y",strtotime($track['system_log'])),
				"list"=>$list,
			);
			
		};
		
		# picked up / received
		$t_icons = array(null,"icon-android-arrow-dropdown","icon-briefcase","icon-ios-location-outline","icon-arrow44","icon-android-folder-open");		
		
		if ($track['track_action_status']!=null) {
		
			if ($track['track_action_status']=="picked up") { # pick up
				
				$status = $track['track_action_status'];
				
				$text = get_staff_name($con,$track['track_action_staff'])." $status the document";
				if (is_picked_up_by_other($track['transit'])) {
					
					$text = get_transit_picked_up_other($track['transit'])." $status the document";
					
				};
				
				$list[] = array(
					"status"=>array(
						"text"=>$text,
						"comment"=>null,
					)
				);
				
				$document_tracks[] = array(
					"track_id"=>$track['id'],			
					"id"=>$track['track_action'],			
					"icon"=>$t_icons[get_transit_id($track['transit'])],
					"bg"=>"bg-danger",
					"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
					"track_date"=>date("M j, Y",strtotime($track['system_log'])),
					"list"=>$list,
				);
				
			};
			
			if ($track['track_action_status']=="received") { # received

				$status = $track['track_action_status'];
				if ($track['track_action_status']=="filed") $status.=" and filed";			

				/* $list[] = array(
					"status"=>array(
						"text"=>get_staff_name($con,$track['track_action_staff'])." $status the document",
						"comment"=>null,
					)
				); */

				$received_next_office[] = array(
					"status"=>array(
						"text"=>get_staff_name($con,$track['track_action_staff'])." $status the document at ".get_office_description($con,$track['office_id']),
						"comment"=>null,
					)
				);
				
				$received_next_office = array_reverse($received_next_office);
				
				$document_tracks[] = array(
					"track_id"=>$track['id'],			
					"id"=>$track['track_action'],			
					"icon"=>$t_icons[get_transit_id($track['transit'])],
					"bg"=>"bg-danger",
					"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
					"track_date"=>date("M j, Y",strtotime($track['system_log'])),
					"list"=>$received_next_office,
				);
				
				$received_next_office = [];

			};

			if ($track['track_action_status']=="released") { # released

				$list[] = array(
					"status"=>array(
						// "text"=>get_staff_name($con,$track['track_action_staff'])." ".$track['track_action_status']." the document to ".get_transit_staff($con,$track['transit'],"released_to"),
						"text"=>"The document was ".$track['track_action_status']." to the ".get_transit_office($con,$track['transit']),
						"comment"=>null,
					)
				);
				
				$document_tracks[] = array(
					"track_id"=>$track['id'],			
					"id"=>$track['track_action'],			
					"icon"=>$t_icons[get_transit_id($track['transit'])],
					"bg"=>"bg-danger",
					"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
					"track_date"=>date("M j, Y",strtotime($track['system_log'])),
					"list"=>$list,
				);
				
			};
			
			if ($track['track_action_status']=="filed") { # filed

				$status = $track['track_action_status'];		

				$list[] = array(
					"status"=>array(
						"text"=>get_staff_name($con,$track['track_action_staff'])." $status the document",
						"comment"=>null,
					)
				);

				$document_tracks[] = array(
					"track_id"=>$track['id'],			
					"id"=>$track['track_action'],			
					"icon"=>$t_icons[get_transit_id($track['transit'])],
					"bg"=>"bg-danger",
					"track_time"=>date("h:i:s A",strtotime($track['system_log'])),
					"track_date"=>date("M j, Y",strtotime($track['system_log'])),
					"list"=>$list,
				);

			};
			
		};

	};

	# first track
	$initial_list[] = array(
		"status"=>array(
			"text"=>"Received at ".get_office_description($con,$initial_office)." by ".get_staff_name($con,$document['user_id']),
			"comment"=>null,
		)
	);
	if (count($for_initial)) $initial_list[] = $for_initial;
	if (count($for_signature)) $initial_list[] = $for_signature;	

	$document_tracks[] = array(
		"track_id"=>0,
		"id"=>0,
		"icon"=>"icon-android-arrow-dropdown",
		"bg"=>"bg-warning",
		"track_time"=>date("h:i:s A",strtotime($document['document_date'])),
		"track_date"=>date("M j, Y",strtotime($document['document_date'])),
		"list"=>$initial_list,
	);

	return $document_tracks;

};

?>