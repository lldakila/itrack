<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once 'tracks.php';

$con = new pdo_db("documents");

$document = $con->getData("SELECT documents.id, CONCAT(documents.barcode, ' ', documents.doc_name) text, documents.barcode, documents.doc_name, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT CONCAT(transactions.transaction, ' (', transactions.days, ' working days)') FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction_description, document_transaction_type, document_date FROM documents WHERE documents.barcode = '".$_POST['barcode']."'");
$document = $document[0];

$transaction = $con->getData("SELECT days FROM transactions WHERE id = ".$document['document_transaction_type']);
$days = $transaction[0]['days'];

$document_date = $document['document_date'];

$date = less_weekends($document_date,date("Y-m-d H:i:s"));
$document['elapsed_date_time'] = date_diff_f($document_date,$date);

$due_date = due_date($document_date,$days);
$document['due_date'] = date("F j, Y h:i A",strtotime($due_date));

if (strtotime($date)>=strtotime($due_date)) $document['remaining_before_due'] = "Document is past due";
else $document['remaining_before_due'] = date_diff_f($due_date,$date);

$document['document_date'] = date("F j, Y h:i A",strtotime($document['document_date']));

# tracks

$sql = "SELECT tracks.id, tracks.document_status, IFNULL(tracks.track_office,0) track_office, tracks.document_status_user, tracks.track_option, tracks.document_tracks_status, tracks.track_date, IFNULL(tracks.route_office,0) route_office, tracks.route_user, tracks.track_option FROM tracks LEFT JOIN documents ON tracks.document_id = documents.id WHERE documents.barcode = '".$_POST['barcode']."' ORDER BY id DESC";
$tracks = $con->getData($sql);

$last_track = $tracks[0];
if ($last_track['document_status'] == "Filed") {
	$document['due_date'] = "";
	$document['remaining_before_due'] = "";
};

$tracks = tracks($con,$tracks);

$response = array("document"=>$document,"tracks"=>$tracks);

echo json_encode($response);

?>