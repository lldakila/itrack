<?php

require_once '../db.php';
require_once '../system_privileges.php';
require_once '../classes.php';

session_start();

$con = new pdo_db("users");

$user = $con->get(["id"=>$_SESSION['id']],["employee_id","CONCAT(fname, ' ', lname) user","group_id","div_id"]);

$dir = "pictures/";
$avatar = $dir."avatar.png";

$picture = $dir.$user[0]['employee_id'].".jpg";
if (!file_exists("../".$picture)) $picture = $avatar;

$con->table = "groups";
$group_privileges = $con->get(array("id"=>$user[0]['group_id']),["privileges"]);

$pages_access = [];
if (count($group_privileges)) {
	if ($group_privileges[0]['privileges']!=NULL) {

		$privileges_obj = new privileges(system_privileges,$group_privileges[0]['privileges']);
		$pages_access = $privileges_obj->getPagesPrivileges();

	};
}

$profile = array(
	"user"=>$user[0]['user'],
	"group"=>$user[0]['group_id'],
	"office"=>$user[0]['div_id'],
	"picture"=>$picture,
	"pages_access"=>$pages_access
);

echo json_encode($profile);

?>