<?php

require_once '../db.php';

$con = new pdo_db("users");

$staffs = $con->all(["id","CONCAT(fname, ' ', lname) fullname"]);

echo json_encode($staffs);

?>