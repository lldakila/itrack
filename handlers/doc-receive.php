<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once '../assignments.php';
require_once 'notify.php';

$assignments = assignments;

session_start();

$con = new pdo_db("tracks");

$track_date = date("Y-m-d H:i:s");

# notification
$notifications = [];

$liaisons = $con->getData("SELECT id FROM users WHERE div_id = ".$_SESSION['office']." AND group_id IN ".getAssignmentIds($assignments['group'],1,"group"));
$office = $con->getData("SELECT id, office FROM offices WHERE id = ".$_SESSION['office']);
$receive_by = $con->getData("SELECT CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$_SESSION['id']);
#

# preceding_track
$preceding_track = 0;
if (count($_POST['tracks'])) $preceding_track = $_POST['tracks'][0]['id'];

switch ($_POST['next']['opt']) {

	case "transaction":

		$document_status = "Received";
		$document_tracks_status = "transaction";

		$track = array(
			"document_id"=>$_POST['document']['id'],
			"document_status"=>$document_status, # document status
			"document_status_user"=>$_SESSION['id'],
			"document_tracks_status"=>$document_tracks_status, # tracks status
			"track_office"=>$_SESSION['office'],
			"track_date"=>$track_date,
			"preceding_track"=>$preceding_track
		);

		$con->insertData($track);
		$track_id = $con->insertId;

		# notify
		foreach ($liaisons as $liaison) {
			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$liaison['id'],
				"track_id"=>intval($track_id),
				"notification_type"=>"incoming",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was received at ".$office[0]['office']."<br>by ".$receive_by[0]['fullname']."  on ".date("F j, Y h:i A",strtotime($track_date))
			);
		};

	break;

	case "filed":

		$document_status = "Received";
		$document_tracks_status = "transaction";

		$track = array(
			"document_id"=>$_POST['document']['id'],
			"document_status"=>$document_status, # document status
			"document_status_user"=>$_SESSION['id'],
			"document_tracks_status"=>$document_tracks_status, # tracks status
			"track_office"=>$_SESSION['office'],
			"track_date"=>$track_date,
			"preceding_track"=>$preceding_track
		);

		$con->insertData($track);
		$track_id = $con->insertId;

		# notify
		foreach ($liaisons as $liaison) {
			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$liaison['id'],
				"track_id"=>intval($track_id),
				"notification_type"=>"incoming",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was received at ".$office[0]['office']."<br>by ".$receive_by[0]['fullname']."  on ".date("F j, Y h:i A",strtotime($track_date))
			);
		};		

		$document_status = "Filed";
		$document_tracks_status = "filed";	
		$preceding_track = $track_id;

		$track = array(
			"document_id"=>$_POST['document']['id'],
			"document_status"=>$document_status, # document status
			"document_status_user"=>$_SESSION['id'],
			"document_tracks_status"=>$document_tracks_status, # tracks status
			"track_office"=>$_SESSION['office'],
			"track_date"=>$track_date,
			"preceding_track"=>$preceding_track
		);

		$con->insertData($track);
		$track_id = $con->insertId;		

		# notify
		foreach ($liaisons as $liaison) {
			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$liaison['id'],
				"track_id"=>intval($track_id),
				"notification_type"=>"incoming",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was filed <br>by ".$receive_by[0]['fullname']." at ".$office[0]['office']." on ".date("F j, Y h:i A",strtotime($track_date))
			);
		};		

	break;

};

if (count($notifications)) {
	notify($con,$notifications);	
};

?>