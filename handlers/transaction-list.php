<?php

require_once '../db.php';

$con = new pdo_db("transactions");

$trans = $con->all(["id","transaction","days","shortname"]);

echo json_encode($trans);

?>