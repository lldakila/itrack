<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("transactions");

$tran = $con->getData("SELECT id,transaction,days,shortname FROM transactions WHERE id = ".$_POST['id']);

echo json_encode($tran[0]);

?>