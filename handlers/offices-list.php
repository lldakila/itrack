<?php

require_once '../db.php';

$con = new pdo_db("offices");

$offices = $con->all(["offices.id","(SELECT departments.dept FROM departments WHERE departments.id = offices.dept_id) dept","offices.office","offices.shortname"]);

echo json_encode($offices);

?>