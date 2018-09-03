<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

session_start();

$con = new pdo_db("tracks");

$filter = "WHERE documents.id = ".$_POST['id'];

$sql = "SELECT documents.id, documents.barcode, documents.doc_name, tracks.document_status, tracks.document_status_user, tracks.document_tracks_status, tracks.track_office, tracks.route_user, tracks.track_date, tracks.track_option, tracks.remarks, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) document_transaction_type FROM documents LEFT JOIN tracks ON documents.id = tracks.document_id {$filter} ORDER BY tracks.track_date DESC LIMIT 1";
$incomings = $con->getData($sql);

foreach ($incomings as $i => $incoming) {

	$status = $incoming['document_status'];

	if ($incoming['track_option'] != NULL) {
		$track_option = $con->getData("SELECT id, choice FROM options WHERE id = ".$incoming['track_option']);
		$status = $track_option[0]['choice'];
	};

	if ( ($incoming['document_status'] == "Received") && ($incoming['document_tracks_status'] == "transaction") ) {
		$document_status_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$incoming['document_status_user']);
		$status .= " by ".$document_status_user[0]['fullname'];
	};

	if ($incoming['document_tracks_status'] == "for_pick_up") {
		$track_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$incoming['track_office']);
		$status .= " and for pick up from ".$track_office[0]['office'];
	};	

	if ($incoming['document_tracks_status'] == "incoming") {
		$track_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$incoming['track_office']);		
		$route_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$incoming['route_user']);
		$status = "Picked up from ".$track_office[0]['office']." by ".$route_user[0]['fullname'];
	};	

	$incomings[$i]['document_status'] = $status;	
	$incomings[$i]['track_date'] = date("F j, Y h:i A",strtotime($incoming['track_date']));

};

echo json_encode($incomings[0]);

?>