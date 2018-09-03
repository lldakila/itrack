<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("transactions");

$_POST['id'] = $_POST['id'];

if ($_POST['id']) { # update

	$con->updateData($_POST,'id');	

} else { # insert

	unset($_POST['id']);
	$con->insertData($_POST);

}

?>