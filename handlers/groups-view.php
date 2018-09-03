<?php
$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("groups");

$groups = $con->get(array("id"=>$_POST['id']));

echo json_encode($groups[0]);

?>