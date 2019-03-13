<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../vendor/autoload.php';
require_once '../../db.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$container = new \Slim\Container;
$app = new \Slim\App($container);

$container = $app->getContainer();
$container['con'] = function ($container) {
	$con = new pdo_db();
	return $con;
};

# list accounts
$app->post('/fetch', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "notifications";

	require_once '../../notify.php';	
	
	session_start();	
	
	$session_user_id = $_SESSION['itrack_user_id'];	
	
	$data = $request->getParsedBody();
	
	$and = "";
	if (isset($data['date'])) $and = "AND system_log LIKE '".date("Y-m-d",strtotime($data['date']))."%'";
	
	$notifications = $con->getData("SELECT * FROM notifications WHERE user_id = $session_user_id AND dismiss = 0 $and ORDER BY system_log DESC");
	
	foreach ($notifications as $i => $notification) {
	
		$notifications[$i]['ago'] = ago($notification['system_log']);
		$notifications[$i]['datetime'] = date("M j, Y h:i A",strtotime($notification['system_log']));
		
	};

    return $response->withJson($notifications);

});

$app->get('/hide/{id}', function (Request $request, Response $response, array $args) {
	
	$con = $this->con;
	$con->table = "notifications";

	$id = $args['id'];

	$hide = $con->updateData(array("id"=>$id,"dismiss"=>1,"last_modified"=>"CURRENT_TIMESTAMP"),'id');	

});

$app->post('/hide/seen/{id}', function (Request $request, Response $response, array $args) {
	
	$con = $this->con;
	$con->table = "notifications";

	require_once '../../functions.php';
	
	session_start();

	$session_user_id = $_SESSION['itrack_user_id'];
	$session_office = $_SESSION['office'];		
	
	$id = $args['id'];
	$data = $request->getParsedBody();
	
	$hide = $con->updateData(array("id"=>$id,"dismiss"=>1,"last_modified"=>"CURRENT_TIMESTAMP"),'id');
	
	$staffs = json_decode($data['inform_seen'],true);
	
	$seener = get_staff_name($con,$session_user_id);

	foreach ($staffs as $staff) {

		$notification = array(
			"doc_id"=>$data['doc_id'],
			"revision_id"=>$data['revision_id'],
			"user_id"=>$staff,
			"icon"=>"icon-android-folder-open",
			"icon_bg"=>"icon-bg-circle",
			"icon_color"=>"bg-info",
			"header"=>$data['header'],
			"header_color"=>"cyan darken-3",
			"message"=>"Notification <strong>\"".$data['message']."\"</strong> was seen by $seener",
			"url"=>$data['url'],
		);

		$notify = $con->insertData($notification);

	};	

});

$app->run();

?>