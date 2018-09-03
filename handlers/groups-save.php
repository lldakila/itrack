<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once '../classes.php';

$con = new pdo_db("groups");

$privileges = [];
if (isset($_POST['privileges'])) {
	
	$arrayHex = new ArrayHex();
		
	$privileges = $arrayHex->toHex(json_encode($_POST['privileges']));
	$_POST['group']['privileges'] = $privileges;
	
};

if ($_POST['group']['id']) { # update

	$con->updateData($_POST['group'],'id');	

} else { # insert

	unset($_POST['group']['id']);
	$con->insertData($_POST['group']);

}

?>