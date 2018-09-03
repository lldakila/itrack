<?php

require_once '../db.php';

$con = new pdo_db("options");

$options = $con->all(['id','pre_phrase','choice','description']);

echo json_encode($options);

?>