<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db("documents");

$delete = array("id"=>$_POST['id']);

$files = $con->getData("SELECT file_name FROM files WHERE document_id = ".$_POST['id']);
$attachments = $con->getData("SELECT file_name FROM attachments WHERE document_id = ".$_POST['id']);

// delete files
$files_dir = "../files/";
foreach ($files as $file) {
	
	if (file_exists($files_dir.$file['file_name'])) unlink($files_dir.$file['file_name']);
	
};

// delete attachments
$attachments_dir = "../attachments/";
foreach ($attachments as $attachment) {

	if (file_exists($attachments_dir.$attachment['file_name'])) unlink($attachments_dir.$attachment['file_name']);	

};

$con->deleteData($delete);

?>