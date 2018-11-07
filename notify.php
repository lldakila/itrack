<?php

function notify($con,$notifications) {

	$old_table = $con->table;	
	$con->table = "notifications";

	$notify = $con->insertDataMulti($notifications);

	$con->table = $old_table;

};

function ago($d) {
	
	$now = date_create(date("Y-m-d H:i:s"));
	$date = date_create($d);

	$interval = date_diff($date,$now);

	$s = '';
	if (($interval->i)>1) $s = 's';
	$ago = $interval->format("%i minute$s ago");
	if (($interval->h)>0) {
		$s = '';
		if (($interval->h)>1) $s = 's';		
		$ago = $interval->format("%h hour$s ago");
	};
	if (($interval->d)>0) {
		$s = '';
		if (($interval->d)>1) $s = 's';
		$ago = $interval->format("%d day$s ago");
	};
	if (($interval->m)>0) {
		$s = '';
		if (($interval->m)>1) $s = 's';
		$ago = $interval->format("%m month$s ago");
	};	
	
	return $ago;
	
};

?>