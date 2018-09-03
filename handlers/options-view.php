<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("options");

$opt = $con->getData("SELECT id,choice FROM options WHERE id = ".$_POST['id']);

echo json_encode($opt[0]);

?>