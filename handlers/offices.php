<?php

require_once '../db.php';

$con = new pdo_db("offices");

$offices = $con->all(['id','office','shortname']);

$con->table = "users";
foreach ($offices as $i => $office) {
	
	$offices[$i]['staffs'] = $con->get(["div_id"=>$office['id']],["id","CONCAT(fname, ' ', lname) fullname"]);
	
};

echo json_encode($offices);

?>