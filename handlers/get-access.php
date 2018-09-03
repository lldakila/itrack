<?php

require_once '../db.php';
require_once '../system_privileges.php';
require_once '../classes.php';

function getAccess($con,$mod,$prop) {

$group_privileges = $con->get(array("id"=>$_POST['group']),["privileges"]);

$access = array("value"=>false);

if (count($group_privileges)) {
	if ($group_privileges[0]['privileges']!=NULL) {

		$privileges_obj = new privileges(system_privileges,$group_privileges[0]['privileges']);
		$access = array("value"=>$privileges_obj->hasAccess($mod,$prop));

	};
};

};

?>