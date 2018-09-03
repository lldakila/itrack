<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once 'documents/incomings.php';

session_start();

$incomings = incomings();

echo json_encode($incomings);

?>