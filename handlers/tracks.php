<?php

require_once 'datetime.php';

function tracks($con,$tracks) {

	$status_arr = array(
		"Routed"=>"to"
	);

	foreach ($tracks as $i => $track) {

		$document_status = $track['document_status'];

		$user = $con->getData("SELECT CONCAT(users.fname, ' ', users.lname) user FROM users WHERE users.id = ".$track['document_status_user']);
		$track_office = $con->getData("SELECT offices.office FROM offices WHERE offices.id = ".$track['track_office']);
		$route_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$track['route_office']);			

		$prepo = (isset($status_arr[$document_status]))?$status_arr[$document_status]:"at";

		$track_options = $con->getData("SELECT * FROM tracks_options WHERE track_id = ".$track['id']);
		if (count($track_options)) {
			$document_status = "";
			$options = [];
			foreach ($track_options as $key => $track_option) {
				$option = $con->getData("SELECT id, pre_phrase, choice, description FROM options WHERE id = ".$track_option['track_option']);
				$options[] = $option[0];
				if ($key == 0) {
					$document_status .= ucfirst($option[0]['description']);
					continue;
				};
				$second_pre = "";
				if (($options[0]['pre_phrase']==null)&&($key==1)) $second_pre = "Flagged as ";
				$document_status .= " / $second_pre".$option[0]['choice'];
			};
		};

		$tracks[$i]['track_date_f'] = date("(D) F j, Y",strtotime($track['track_date']));
		$tracks[$i]['track_time_f'] = date("h:i A",strtotime($track['track_date']));

		$track_office_name = (count($track_office))?$track_office[0]['office']:$route_office[0]['office'];

		$status = $document_status." $prepo $track_office_name by ".$user[0]['user']." on ".$tracks[$i]['track_date_f']." ".$tracks[$i]['track_time_f'];

		if ($track['document_tracks_status'] == "for_pick_up") {
			$status = "For pick up for ".$route_office[0]['office']." on ".$tracks[$i]['track_date_f']." ".$tracks[$i]['track_time_f'];
		};

		if ($track['document_tracks_status'] == "incoming") {
			$route_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$track['route_user']);		
			$status = "Picked up by ".$route_user[0]['fullname']." at ".$track_office[0]['office']." on ".$tracks[$i]['track_date_f']." ".$tracks[$i]['track_time_f'];
		};

		$tracks[$i]['status'] = $status;
		$tracks[$i]['interval'] = "";
		
		# Interval between tracks
		if ($i < (count($tracks)-1)) {
			
			$previous_track_date = $tracks[$i+1]['track_date'];
			$track_date = $track['track_date'];
			$interval = date_diff_f($previous_track_date,less_weekends_tracks($previous_track_date,$track_date));
			$tracks[$i]['interval'] = $interval;
			
		};
		
		// less_weekends($date,date("Y-m-d H:i:s"));		
		
		$date = ($i==0)?date("Y-m-d H:i:s"):$tracks[$i-1]['track_date'];
		$track_date = $track['track_date'];
		$tracks[$i]['elapsed_date_time'] = date_diff_f($track_date,less_weekends_tracks($track_date,$date));

	};

	return $tracks;

};

?>