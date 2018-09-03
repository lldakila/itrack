<?php
$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("offices");

$office = $con->getData("SELECT * FROM offices WHERE id = ".$_POST['id']);

if ($office[0]['dept_id']==null) $office[0]['dept_id'] = 0;

$dept_id = $con->getData("SELECT id, dept, shortname FROM departments WHERE id = ".$office[0]['dept_id']);

$office[0]['dept_id'] = (count($dept_id))?$dept_id[0]:array("id"=>0,"dept"=>"","shortname"=>"");

echo json_encode($office[0]);

?>