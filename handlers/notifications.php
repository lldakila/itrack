<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

session_start();

$con = new pdo_db("notifications");

$outgoing = [];
$transaction = [];
$incoming = [];

$response = array(
	"count"=>0,
	"outgoing"=>[],	
	"transaction"=>[],
	"incoming"=>[],
);

$outgoing = $con->getData("SELECT notifications.id, notifications.doc_id, notifications.message, notifications.system_log, tracks.document_tracks_status, tracks.track_office FROM notifications LEFT JOIN tracks ON notifications.track_id = tracks.id WHERE dismiss = 0 AND notification_type = 'outgoing' AND user_id = ".$_SESSION['id']." ORDER BY system_log DESC");
foreach ($outgoing as $key => $o) {
	$outgoing[$key]['show'] = true;
	if ($o['document_tracks_status'] == "incoming") $outgoing[$key]['show'] = false;	
};

$transaction = $con->getData("SELECT notifications.id, notifications.doc_id, notifications.message, notifications.system_log, tracks.document_tracks_status, tracks.track_office FROM notifications LEFT JOIN tracks ON notifications.track_id = tracks.id WHERE dismiss = 0 AND notification_type = 'transaction' AND user_id = ".$_SESSION['id']." ORDER BY system_log DESC");
foreach ($transaction as $key => $t) {
	$transaction[$key]['show'] = true;
};

$incoming = $con->getData("SELECT notifications.id, notifications.doc_id, notifications.message, notifications.system_log, tracks.document_tracks_status, tracks.track_office FROM notifications LEFT JOIN tracks ON notifications.track_id = tracks.id WHERE dismiss = 0 AND notification_type = 'incoming' AND user_id = ".$_SESSION['id']." ORDER BY system_log DESC");
foreach ($incoming as $key => $t) {
	$incoming[$key]['show'] = false;
	if ($t['document_tracks_status'] == "incoming") $incoming[$key]['show'] = true;
};

$response['count'] = count($outgoing)+count($transaction)+count($incoming);
$response['outgoing'] = $outgoing;
$response['transaction'] = $transaction;
$response['incoming'] = $incoming;

echo json_encode($response);

?>