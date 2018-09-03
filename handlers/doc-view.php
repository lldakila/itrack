<?php
$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("document_types");

$doc = $con->get(array("id"=>$_POST['id']));

$doc_id = ($doc[0]['doc_id'])?$doc[0]['doc_id']:0;

$document_type = $con->getData("SELECT id, document_type FROM document_types WHERE id = $doc_id");

$doc[0]['doc_id'] = ($doc[0]['doc_id'])?$document_type[0]:array("id"=>0,"document_type"=>"");

echo json_encode($doc[0]);

?>