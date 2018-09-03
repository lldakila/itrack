<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

session_start();

$con = new pdo_db("tracks");

$filter = (isset($_POST['filter']))?" WHERE documents.id = ".$_POST['id']." AND documents.barcode LIKE '%".$_POST['filter']."%'":"";

$documents = $con->getData("SELECT documents.id, documents.barcode FROM documents".$filter);

echo json_encode($documents);

?>