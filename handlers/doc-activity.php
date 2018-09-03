<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once 'datetime.php';

session_start();

$con = new pdo_db();

$sql = "SELECT documents.id, documents.origin office_origin, documents.barcode, documents.doc_name, documents.document_date, tracks.document_status, tracks.document_status_user, tracks.document_tracks_status, tracks.track_office, tracks.track_date, tracks.track_option, tracks.route_office, tracks.route_user, tracks.remarks, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT CONCAT(transactions.transaction, ' (', transactions.days, ' working days)') FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction_description, document_transaction_type FROM documents LEFT JOIN tracks ON documents.id = tracks.document_id WHERE documents.id = ".$_POST['id'];
$document = $con->getData($sql);

# elapsed_date_time/due_date/remaining_before_due
$transaction = $con->getData("SELECT days FROM transactions WHERE id = ".$document[0]['document_transaction_type']);
$days = $transaction[0]['days'];		

$document_date = $document[0]['document_date'];

$date = less_weekends($document_date,date("Y-m-d H:i:s"));
$document[0]['elapsed_date_time'] = date_diff_f($document_date,$date);

$due_date = due_date($document_date,$days);
$document[0]['due_date'] = date("F j, Y h:i A",strtotime($due_date));

if (strtotime($date)>=strtotime($due_date)) $document[0]['remaining_before_due'] = "Document is past due";
else $document[0]['remaining_before_due'] = date_diff_f($due_date,$date);
#

$document[0]['document_date'] = date("F j, Y h:i A",strtotime($document[0]['document_date']));

$tracks = $con->getData("SELECT id, track_date, document_status ds, document_status, document_tracks_status, document_status_user, track_option, IFNULL(track_office,0) track_office, IFNULL(route_office,0) route_office, route_user, (SELECT CONCAT(users.fname, ' ', users.lname) FROM users WHERE users.id = tracks.document_status_user) staff FROM tracks WHERE document_id = ".$_POST['id']." ORDER BY tracks.id DESC");

$last_track = $tracks[0];
if ($last_track['document_status'] == "Filed") {
	$document[0]['due_date'] = "";
	$document[0]['remaining_before_due'] = "";
};

foreach ($tracks as $i => $track) {

	$status = $track['document_status'];

	$track_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$track['track_office']);
	$route_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$track['route_office']);	

	$track_office_name = (count($track_office))?$track_office[0]['office']:"";
	$route_office_name = (count($route_office))?$route_office[0]['office']:"";

	$office = $track_office_name;

	$track_options = $con->getData("SELECT * FROM tracks_options WHERE track_id = ".$track['id']);
	if (count($track_options)) {
		$status = "";
		$document_status_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$track['document_status_user']);
		$options = [];		
		foreach ($track_options as $key => $track_option) {
			$option = $con->getData("SELECT id, pre_phrase, choice FROM options WHERE id = ".$track_option['track_option']);
			$options[] = $option[0];
			if ($key == 0) {
				$status .= $option[0]['pre_phrase']." ".$option[0]['choice'];
				continue;
			};
			$second_pre = "";
			if (($options[0]['pre_phrase']==null)&&($key==1)) $second_pre = "Flagged as ";
			$status .= " / $second_pre".$option[0]['choice'];
		};
		$status .= " by ".$document_status_user[0]['fullname'];		
	};

	if ( ($track['document_status'] == "Received") && ($track['document_tracks_status'] == "transaction") ) {
		$document_status_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$track['document_status_user']);
		$status .= " by ".$document_status_user[0]['fullname'];
		$office = $track_office_name;
	};		

	if ($track['document_tracks_status'] == "for_pick_up") {
		$status = "For pick up for ".$route_office[0]['office'];
		$office = $route_office_name;		
	};

	if ($track['document_tracks_status'] == "incoming") {
		$route_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$track['route_user']);		
		$status = "Picked up by ".$route_user[0]['fullname'];
	};

	if ($track['document_tracks_status'] == "filed") {
		$document_status_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$track['document_status_user']);
		$status = "Filed by ".$document_status_user[0]['fullname'];
	};	

	$tracks[$i]['date'] = date("F j, Y",strtotime($track['track_date']));
	$tracks[$i]['time'] = date("h:i:s A",strtotime($track['track_date']));

	$tracks[$i]['document_status'] = $status;
	$tracks[$i]['office'] = $office;

};

$files = $con->getData("SELECT id, file_name FROM files WHERE document_id = ".$_POST['id']);

foreach ($files as $i => $file) {
	
	$ft = explode(".",$file['file_name']);
	
	$files[$i]['type'] = $ft[1];
	$files[$i]['path'] = "../files/".$file['file_name'];
	
};

$attachments = $con->getData("SELECT id, file_name FROM attachments WHERE document_id = ".$_POST['id']);

foreach ($attachments as $i => $attachment) {
	
	$ft = explode(".",$attachment['file_name']);
	
	$attachments[$i]['type'] = $ft[1];
	$attachments[$i]['path'] = "../attachments/".$attachment['file_name'];
	
};

echo json_encode(array("document"=>$document[0],"tracks"=>$tracks,"files"=>$files,"attachments"=>$attachments));

?>