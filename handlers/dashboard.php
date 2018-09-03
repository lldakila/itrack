<?php

session_start();

require_once 'documents/outgoings.php';
require_once 'documents/incomings.php';
require_once 'documents/transactions.php';

$documents = array(
	"outgoings"=>[],
	"incomings"=>[],
	"transactions"=>[]
);

$outgoings = outgoings();
$incomings = incomings();
$transactions = transactions();

$documents['outgoings'] = $outgoings;
$documents['incomings'] = $incomings;
$documents['transactions'] = $transactions;

echo json_encode($documents);

?>