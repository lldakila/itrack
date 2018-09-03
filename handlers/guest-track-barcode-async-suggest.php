<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

session_start();

$con = new pdo_db("tracks");

$filter = (isset($_POST['filter']))?" WHERE documents.barcode LIKE '%".$_POST['filter']."%'":"";

$documents = $con->getData("SELECT documents.id, documents.barcode text, documents.barcode, documents.doc_name, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type, (SELECT offices.office FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) document_transaction_type, DATE_FORMAT(documents.document_date, '%M %d, %Y') document_date FROM documents".$filter);

echo json_encode($documents);

?>