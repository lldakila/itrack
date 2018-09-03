<?php

require_once '../db.php';
require_once 'datetime.php';

function outgoings() {

	$con = new pdo_db("tracks");

	$outgoings = [];

	# Origination from an office

	$filter = "WHERE tracks.id = (SELECT MAX(tracks.id) FROM tracks WHERE tracks.document_id = documents.id)";
	$filter .= " AND tracks.track_office != ".$_SESSION['office']." AND documents.origin = ".$_SESSION['office']." AND document_tracks_status = 'transaction'";

	$sql = "SELECT documents.id, documents.origin office_origin, documents.barcode, documents.doc_name, documents.document_date, tracks.document_status, tracks.document_status_user, tracks.document_tracks_status, tracks.track_office, tracks.track_date, tracks.track_option, tracks.route_office, tracks.route_user, tracks.remarks, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction_description, document_transaction_type FROM documents LEFT JOIN tracks ON documents.id = tracks.document_id {$filter} ORDER BY tracks.track_date DESC";
	$originating = $con->getData($sql);

	foreach ($originating as $o) {
		
		$outgoings[] = $o;
		
	};

	# For pick to other offices

	$filter = "WHERE tracks.id = (SELECT MAX(tracks.id) FROM tracks WHERE tracks.document_id = documents.id)";
	$filter .= " AND tracks.track_office = ".$_SESSION['office']." AND document_tracks_status = 'for_pick_up'";

	$sql = "SELECT documents.id, documents.origin office_origin, documents.barcode, documents.doc_name, documents.document_date, tracks.document_status, tracks.document_status_user, tracks.document_tracks_status, tracks.track_office, tracks.track_date, tracks.track_option, tracks.route_office, tracks.route_user, tracks.remarks, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction_description, document_transaction_type FROM documents LEFT JOIN tracks ON documents.id = tracks.document_id {$filter} ORDER BY tracks.track_date DESC";
	$transactions = $con->getData($sql);

	foreach ($transactions as $i => $t) {

		$outgoings[] = $t;	

	};

	foreach ($outgoings as $i => $outgoing) {

		$status = $outgoing['document_status'];
		
		$outgoings[$i]['show_action'] = true;	

		if ($outgoing['track_option'] != NULL) {
			$track_option = $con->getData("SELECT id, choice FROM options WHERE id = ".$outgoing['track_option']);
			$status = $track_option[0]['choice'];
		};
		
		if ( ($outgoing['document_status'] == "Received") && ($outgoing['document_tracks_status'] == "transaction") ) {
			$document_status_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$outgoing['document_status_user']);
			$status .= " by ".$document_status_user[0]['fullname'];
		};
		
		if ($outgoing['document_tracks_status'] == "for_pick_up") {
			$track_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$outgoing['track_office']);		
			$status .= " and for pick up from ".$track_office[0]['office'];
		};	

		if ($outgoing['document_tracks_status'] == "incoming") {
			$track_office = $con->getData("SELECT id, office FROM offices WHERE id = ".$outgoing['track_office']);		
			$route_user = $con->getData("SELECT id, CONCAT(fname, ' ', lname) fullname FROM users WHERE id = ".$outgoing['route_user']);		
			$status = "Picked up from ".$track_office[0]['office']." by ".$route_user[0]['fullname'];
		};
		
		# elapsed_date_time/due_date/remaining_before_due
		$transaction = $con->getData("SELECT days FROM transactions WHERE id = ".$outgoing['document_transaction_type']);
		$days = $transaction[0]['days'];		
		
		$date = date("Y-m-d H:i:s");
		$document_date = $outgoing['document_date'];
		$outgoings[$i]['elapsed_date_time'] = date_diff_f($document_date,$date);

		$due_date = date("Y-m-d H:i:s",strtotime("+$days Days",strtotime($document_date)));
		$outgoings[$i]['due_date'] = date("F j, Y h:i A",strtotime($due_date));

		if (strtotime($date)>=strtotime($due_date)) $outgoings[$i]['remaining_before_due'] = "Document is past due";
		else $outgoings[$i]['remaining_before_due'] = date_diff_f($date,$due_date);
		#
		
		$outgoings[$i]['document_status'] = $status;	
		$outgoings[$i]['track_date'] = date("F j, Y h:i A",strtotime($outgoing['track_date']));
		$outgoings[$i]['document_date'] = date("F j, Y h:i A",strtotime($outgoing['document_date']));
		
		if ($outgoing['office_origin'] == $_SESSION['office']) $outgoings[$i]['show_action'] = false;		

	};
	
	return $outgoings;

};

?>