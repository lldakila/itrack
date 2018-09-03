<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

session_start();

$con = new pdo_db("documents");

$archives = [];

$filter = " WHERE tracks.id = (SELECT MAX(tracks.id) FROM tracks WHERE documents.id = tracks.document_id)";
$filter .= " AND tracks.document_tracks_status = 'filed' AND (documents.origin = ".$_SESSION['office']." OR tracks.track_office = ".$_SESSION['office'].")";

$sql = "SELECT documents.id, documents.doc_name, documents.barcode, documents.document_date, (SELECT offices.shortname FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT communications.shortname FROM communications WHERE communications.id = documents.communication) communication, documents.remarks FROM documents LEFT JOIN tracks ON tracks.document_id = documents.id{$filter}";
$docs = $con->getData($sql);

foreach ($docs as $i => $doc) {

	$doc['document_date'] = date("F j, Y h:i A",strtotime($doc['document_date']));
	$archives[] = $doc;

};

echo json_encode($archives);

?>