<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("groups");

$groups = array("id"=>$_POST['id']);

$con->deleteData($groups);

?>