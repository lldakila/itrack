<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once 'documents/outgoings.php';

session_start();

$outgoings = outgoings();

echo json_encode($outgoings);

?>