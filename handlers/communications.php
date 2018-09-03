<?php

require_once '../db.php';

$con = new pdo_db("communications");

$communication = $con->all(['id','communication','shortname']);

echo json_encode($communication);

?>