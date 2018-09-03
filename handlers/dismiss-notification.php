<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("notifications");

$dismiss = $con->updateData(array("id"=>$_POST['id'],"dismiss"=>1,"last_modified"=>"CURRENT_TIMESTAMP"),'id');

?>