<?php

function notify($con,$notifications) {

	$old_table = $con->table;	
	$con->table = "notifications";

	$notify = $con->insertDataMulti($notifications);

	$con->table = $old_table;

};

?>