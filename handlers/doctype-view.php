<?php
$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("document_types");

$doc_type = $con->getData("SELECT id,document_type,shortname FROM document_types WHERE id = ".$_POST['id']);

echo json_encode($doc_type[0]);

?>