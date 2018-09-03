<?php

require_once '../db.php';

$con = new pdo_db("document_types");

$doc_type = $con->all(['id','document_type']);

echo json_encode($doc_type);

?>