<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db();
$sql = "SELECT id, group_id, div_id FROM users WHERE uname = '$_POST[uname]' AND pw = '$_POST[pw]'";
$account = $con->getData($sql);
if (($con->rows) > 0) {
	session_start();
	$_SESSION['id'] = $account[0]['id'];
	$_SESSION['group'] = $account[0]['group_id'];
	$_SESSION['office'] = $account[0]['div_id'];
	echo json_encode(array("login"=>true));
} else {
	echo json_encode(array("login"=>false));
}

?>