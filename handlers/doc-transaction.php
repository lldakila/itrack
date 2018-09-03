<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once '../assignments.php';
require_once 'notify.php';

$assignments = assignments;

session_start();

$con = new pdo_db("tracks");

$document_status = NULL;
$document_tracks_status = "transaction";
$track_office = $_POST['document']['track_office'];
// $track_option = NULL;
$route_office = NULL;
$route_user = NULL;
$remarks = (isset($_POST['next']['remarks']))?$_POST['next']['remarks']:"";

# preceding_track
$preceding_track = 0;
if (count($_POST['tracks'])) $preceding_track = $_POST['tracks'][0]['id'];

$notifications = [];

$track_date = date("Y-m-d H:i:s");

$staff = $con->getData("SELECT CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$_SESSION['id']);

$document_origin = $con->getData("SELECT origin FROM documents WHERE id = ".$_POST['document']['id']);


switch ($_POST['action']) {

	case "Flag":
	
		$get_track_office = $con->getData("SELECT id, office FROM offices WHERE id = $track_office");
		$track_office_name = $get_track_office[0]['office'];	

		$route_office = $_POST['document']['route_office'];
		
		# liaisons
		$liaisons = $con->getData("SELECT id FROM users WHERE div_id = ".$document_origin[0]['origin']." AND group_id IN ".getAssignmentIds($assignments['group'],1,"group"));
		foreach ($liaisons as $liaison) {

			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$liaison['id'],
				"notification_type"=>"outgoing",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was ".getFlags($_POST['options'])." at $track_office_name<br>by ".$staff[0]['fullname']."  on ".date("F j, Y h:i A",strtotime($track_date))
			);

		};
		
		# PS Staffs / AOs
		$aos = $con->getData("SELECT id FROM users WHERE div_id = ".$track_office." AND group_id IN ".getAssignmentIds($assignments['group'],2,"group"));
		foreach ($aos as $ao) {

			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$ao['id'],
				"notification_type"=>"transaction",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was ".getFlags($_POST['options'])." at $track_office_name<br>by ".$staff[0]['fullname']."  on ".date("F j, Y h:i A",strtotime($track_date))
			);

		};		

	break;

	case "Forward":

		$get_track_office = $con->getData("SELECT id, office FROM offices WHERE id = $track_office");
		$track_office_name = $get_track_office[0]['office'];	
	
		$document_status = "Forward";
		$document_tracks_status = "for_pick_up";
		$route_office = $_POST['next']['route_office']['id'];
		// $track_option = $_POST['track_option'];

		# liaisons
		$liaisons = $con->getData("SELECT id FROM users WHERE div_id = $route_office AND group_id IN ".getAssignmentIds($assignments['group'],1,"group"));
		foreach ($liaisons as $liaison) {

			$notifications[] = array(
				"doc_id"=>$_POST['id'],
				"user_id"=>$liaison['id'],
				"notification_type"=>"incoming",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> is ready for pick up at $track_office_name<br>Date: ".date("F j, Y",strtotime($track_date))."<br>Time: ".date("h:i A",strtotime($track_date))
			);

		};
		
		# AOs
		$aos = $con->getData("SELECT id FROM users WHERE div_id = ".$track_office." AND group_id IN ".getAssignmentIds($assignments['group'],2,"group"));
		foreach ($aos as $ao) {

			$notifications[] = array(
				"doc_id"=>$_POST['id'],
				"user_id"=>$ao['id'],
				"notification_type"=>"outgoing",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was marked ready for pick up by ".$staff[0]['fullname']." at $track_office_name<br>Date: ".date("F j, Y",strtotime($track_date))."<br>Time: ".date("h:i A",strtotime($track_date))
			);

		};	
		
	break;

	case "Release":

		$get_track_office = $con->getData("SELECT id, office FROM offices WHERE id = $track_office");
		$track_office_name = $get_track_office[0]['office'];	
	
		$document_status = "Release";
		$document_tracks_status = "incoming";
		$route_office = $_POST['next']['route_office']['id'];
		$route_user = $_POST['next']['route_user']['id'];
		// $track_option = $_POST['document']['track_option'];		

		$pick_up_by = $con->getData("SELECT CONCAT(fname, ' ', lname) fullname FROM users WHERE id = $route_user");

		# liaisons
		$liaisons = $con->getData("SELECT id FROM users WHERE div_id = ".$document_origin[0]['origin']." AND group_id IN ".getAssignmentIds($assignments['group'],1,"group"));
		foreach ($liaisons as $liaison) {

			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$liaison['id'],
				"notification_type"=>"incoming",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was picked up<br>by ".$pick_up_by[0]['fullname']." at $track_office_name on ".date("F j, Y h:i A",strtotime($track_date))
			);

		};

		# AOs
		$aos = $con->getData("SELECT id FROM users WHERE div_id = ".$track_office." AND group_id IN ".getAssignmentIds($assignments['group'],2,"group"));
		foreach ($aos as $ao) {

			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$ao['id'],
				"notification_type"=>"outgoing",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was picked up<br>by ".$pick_up_by[0]['fullname']." at $track_office_name on ".date("F j, Y h:i A",strtotime($track_date))
			);

		};
		
	break;

	case "File":
		
		$track_office = $_POST['document']['track_office'];
		
		$get_track_office = $con->getData("SELECT id, office FROM offices WHERE id = $track_office");
		$track_office_name = $get_track_office[0]['office'];	

		$document_status = "Filed";
		$document_tracks_status = "filed";
		// $track_option = $_POST['document']['track_option'];

		$file_by = $con->getData("SELECT CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$_SESSION['id']);		
		$file_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$_SESSION['office']);
		$file_office_name = $file_office[0]['office'];		
		
		# all staffs
		$all_staffs = $con->getData("SELECT id FROM users WHERE div_id = ".$document_origin[0]['origin']." AND group_id IN ".getAssignmentIds($assignments['group'],1,"group"));
		foreach ($all_staffs as $staff) {

			$notifications[] = array(
				"doc_id"=>$_POST['document']['id'],
				"user_id"=>$staff['id'],
				"notification_type"=>"incoming",
				"message"=>$_POST['document']['doc_type']." with subject: <strong>".$_POST['document']['doc_name']."</strong> was filed <br>by ".$file_by[0]['fullname']." at $file_office_name on ".date("F j, Y h:i A",strtotime($track_date))
			);

		};		
		
	break;

};

$track = array(
	"document_id"=>$_POST['document']['id'],
	"document_status"=>$document_status, # document status
	"document_status_user"=>$_SESSION['id'],
	"document_tracks_status"=>$document_tracks_status, # tracks status
	"track_office"=>$track_office,
	"track_date"=>$track_date,
	"route_office"=>$route_office,
	"route_user"=>$route_user,
	"remarks"=>$remarks,
	"preceding_track"=>$preceding_track
);

function getOption($options) {
	
	$option = NULL;
	
	foreach ($options as $opt) {

		if (isset($opt['value'])) {

			if ($opt['value']) {
				$option = $opt['id'];
				break;
			};

		};

	};

	return $option;
	
};

function getOptionDescription($options,$track_option) {

	$description = NULL;

	foreach ($options as $opt) {

		if ($opt['id'] == $track_option) {

			$description = $opt['description'];

		};

	};

	return $description;	

};

function getFlags($options) {
	
	$flags = "";
	
	$chosen_options = [];
	
	foreach ($options as $option) {
		
		if (isset($option['value'])) {

			if ($option['value']) {

				$chosen_options[] = $option;
			
			};

		};		
		
	};

	$second_pre = "";
	foreach ($chosen_options as $key => $chosen_option) {

		if ($key == 0) {
			$flags .= $chosen_option['pre_phrase']." ".$chosen_option['choice'];
			continue;
		};		

		if (($chosen_options[0]['pre_phrase']==null)&&($key==1)) $second_pre = "Flagged as ";
		$flags .= " / $second_pre".$chosen_option['choice'];		

	};

	return $flags;
	
};

$con->insertData($track);
$track_id = $con->insertId;

# options
$con->table = "tracks_options";
foreach ($_POST['options'] as $option) {
	
	if (isset($option['value'])) {

		if ($option['value']) {

			$track_option = array(
				"track_id"=>$track_id,
				"track_option"=>$option['id']
			);
			
			$con->insertData($track_option);

		};

	};

};

# notify
if (count($notifications)) {
	foreach ($notifications as $i => $notification) {
		$notifications[$i]['track_id'] = intval($track_id);
	};
	notify($con,$notifications);	
};

?>