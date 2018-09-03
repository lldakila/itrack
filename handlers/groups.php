<?php

require_once '../db.php';

$con = new pdo_db("groups");

$groups = $con->all(['id','group_name']);

echo json_encode($groups);

?>