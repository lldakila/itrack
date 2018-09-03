<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once 'datetime.php';

session_start();

$con = new pdo_db("tracks");

$filter = (isset($_POST['filter']))?" WHERE CONCAT(documents.barcode, ' ', documents.doc_name) LIKE '%".$_POST['filter']."%'":"";

$documents = $con->getData("SELECT documents.id, CONCAT(documents.barcode, ' ', documents.doc_name) text, documents.barcode, documents.doc_name, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction_description, document_transaction_type, DATE_FORMAT(documents.document_date, '%M %d, %Y') document_date_f, document_date FROM documents".$filter);

foreach ($documents as $i => $document) {

	$transaction = $con->getData("SELECT days FROM transactions WHERE id = ".$document['document_transaction_type']);
	$days = $transaction[0]['days'];

	$date = date("Y-m-d H:i:s");
	$document_date = $document['document_date'];
	$documents[$i]['elapsed_date_time'] = date_diff_f($document_date,$date);
	
	$due_date = date("Y-m-d H:i:s",strtotime("+$days Days",strtotime($document_date)));
	$documents[$i]['due_date'] = date("F j, Y h:i A",strtotime($due_date));
	
	if (strtotime($date)>=strtotime($due_date)) $documents[$i]['remaining_before_due'] = "Document is past due";
	else $documents[$i]['remaining_before_due'] = date_diff_f($date,$due_date);

};

echo json_encode($documents);

?>