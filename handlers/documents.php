<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once 'documents/transactions.php';

session_start();

$documents = transactions();

echo json_encode($documents);

?>