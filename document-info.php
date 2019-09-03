<?php

function document_info_complete($con,$document) {
	
	// doc_type, origin, other_origin, communication, document_transaction_type
	
	$doc_type = $con->getData("SELECT id, document_type, shortname FROM document_types WHERE id = ".$document['doc_type']);	
	$document['doc_type'] = $doc_type[0];

	$origin = $con->getData("SELECT id, office, shortname, dept_id FROM offices WHERE id = ".$document['origin']);
	if ($document['origin'] == 1) $origin[0]['office'] = $origin[0]['office']." (".$document['other_origin'].")";
	$document['origin'] = $origin[0];
	
	$communication = $con->getData("SELECT id, communication, shortname FROM communications WHERE id = ".$document['communication']);
	$document['communication'] = $communication[0];

	$document_transaction_type = $con->getData("SELECT id, transaction, days, shortname FROM transactions WHERE id = ".$document['document_transaction_type']);
	$document['document_transaction_type'] = $document_transaction_type[0];
	
	return $document;
	
};

function document_info($con,$document) {
	
	// doc_type, origin, other_origin, communication, document_transaction_type
	
	$doc_type = $con->getData("SELECT id, document_type, transaction_id FROM document_types WHERE id = ".$document['doc_type']);	
	$document['doc_type'] = $doc_type[0];
	
	$origin = $con->getData("SELECT id, office, shortname FROM offices WHERE id = ".$document['origin']);
	if ($document['origin'] == 1) $origin[0]['office'] = $origin[0]['office']." (".$document['other_origin'].")";
	$document['origin'] = $origin[0];
	
	$communication = $con->getData("SELECT id, communication, shortname FROM communications WHERE id = ".$document['communication']);
	$document['communication'] = $communication[0];
	
	$document_transaction_type = $con->getData("SELECT id, transaction, days FROM transactions WHERE id = ".$document['document_transaction_type']);
	$document['document_transaction_type'] = $document_transaction_type[0];
	
	return $document;
	
};

?>