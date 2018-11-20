<?php

require_once '../db.php';
require_once '../notify.php';

$con = new pdo_db("notifications");

session_start();

$session_user_id = $_SESSION['itrack_user_id'];	

$notifications = $con->getData("SELECT * FROM notifications WHERE user_id = $session_user_id AND dismiss = 0 ORDER BY system_log DESC");	

foreach ($notifications as $i => $notification) {

	$notifications[$i]['ago'] = ago($notification['system_log']);
	
};

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

echo "data: ".json_encode($notifications)."\n\n";

flush();
	
?>