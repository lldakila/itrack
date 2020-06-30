<?php

require_once '../db.php';
require_once '../notify.php';

$con = new pdo_db("notifications");

session_start();

$session_user_id = $_SESSION['itrack_user_id'];	

$notifications = $con->getData("SELECT notifications.id, notifications.doc_id, notifications.track_id, notifications.revision_id, notifications.user_id, notifications.office_id, notifications.icon, notifications.icon_bg, notifications.icon_color, notifications.header, notifications.header_color, notifications.message, notifications.url, notifications.dismiss, notifications.inform_seen, notifications.system_log, notifications.last_modified, documents.is_rush FROM notifications LEFT JOIN documents ON notifications.doc_id = documents.id WHERE notifications.user_id = $session_user_id AND notifications.dismiss = 0 ORDER BY notifications.id DESC");	

foreach ($notifications as $i => $notification) {

	$notifications[$i]['ago'] = ago($notification['system_log']);
	
};

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

echo "data: ".json_encode($notifications)."\n\n";

flush();
	
?>