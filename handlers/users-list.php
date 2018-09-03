<?php
$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("users");

$users = $con->getData("SELECT *, (SELECT offices.shortname FROM offices WHERE offices.id = users.div_id) div_id, (SELECT groups.group_name FROM groups WHERE groups.id = users.group_id) group_name FROM users ORDER BY users.id");

foreach ($users as $i => $user) {
	
	unset($users[$i]['pw']);
	
};

echo json_encode($users);

?>