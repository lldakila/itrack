<?php
$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("users");

$user = $con->get(array("id"=>$_POST['id']));


$group_id = ($user[0]['group_id'])?$user[0]['group_id']:0;

$group = $con->getData("SELECT id, group_name FROM groups WHERE id = $group_id");

$user[0]['group_id'] = ($user[0]['group_id'])?$group[0]:array("id"=>0,"group_name"=>"");



$div_id = ($user[0]['div_id'])?$user[0]['div_id']:0;

$office = $con->getData("SELECT id, office FROM offices WHERE id = $div_id");

$user[0]['div_id'] = ($office)?$office[0]:array("id"=>0,"office"=>"");

echo json_encode($user[0]);

?>