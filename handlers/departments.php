<?php

require_once '../db.php';

$con = new pdo_db("departments");

$departments = $con->all(['id','shortname','dept']);

echo json_encode($departments);

?>