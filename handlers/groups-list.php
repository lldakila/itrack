<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("groups");

$groups = $con->getData("SELECT * FROM `groups`");

echo json_encode($groups);

?>