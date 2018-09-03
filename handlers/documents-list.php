<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("documents");

$documents = $con->getData("SELECT documents.id, documents.doc_name, documents.barcode, DATE_FORMAT(documents.document_date, '%M %e, %Y') document_date , (SELECT offices.shortname FROM offices WHERE offices.id = documents.origin) origin, (SELECT transactions.transaction FROM transactions WHERE transactions.id = documents.document_transaction_type) transaction, (SELECT document_types.document_type FROM document_types WHERE document_types.id = documents.doc_type) doc_type,(SELECT communications.shortname FROM communications WHERE communications.id = documents.communication) communication, documents.remarks FROM documents");

echo json_encode($documents);

?>