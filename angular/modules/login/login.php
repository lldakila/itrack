<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../../../db.php';

$uname = (isset($_POST['uname']))?$_POST['uname']:"";
$pw = (isset($_POST['pw']))?$_POST['pw']:"";

$con = new pdo_db();
$sql = "SELECT id, group_id, div_id FROM users WHERE uname = '$uname' AND pw = '$pw'";
$account = $con->getData($sql);
if (($con->rows) > 0) {
	session_start();
	$_SESSION['itrack_user_id'] = $account[0]['id'];
	$_SESSION['group'] = $account[0]['group_id'];
	$_SESSION['office'] = $account[0]['div_id'];
	echo json_encode(array("login"=>true));
} else {
	echo json_encode(array("login"=>false));
}

?>