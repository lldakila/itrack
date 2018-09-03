<?php

require_once '../db.php';

$con = new pdo_db("transactions");

$transaction = $con->all(['id','transaction']);

echo json_encode($transaction);

?>