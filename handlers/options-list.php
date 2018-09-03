<?php

require_once '../db.php';

$con = new pdo_db("options");

$opts = $con->all(["id","choice"]);

echo json_encode($opts);

?>