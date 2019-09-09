<?php

function date_diff_f($date1,$date2) {

	$diff = date_diff(date_create($date1),date_create($date2));
	
	$i = (($diff->i)>1)?"s":"";
	$h = (($diff->h)>1)?"s":"";
	$d = (($diff->d)>1)?"s":"";
	$m = (($diff->m)>1)?"s":"";
	$y = (($diff->y)>1)?"s":"";
	$date_diff_f = $diff->format("%i minute$i");
	if (($diff->h)>0) {
		$date_diff_f = $diff->format("%h hour$h and %i minute$i");
	};
	if (($diff->d)>0) {
		$date_diff_f = $diff->format("%d day$d, %h hour$h and %i minute$i");
	};
	if (($diff->m)>0) {
		$date_diff_f = $diff->format("%m month$m, %d day$d, %h hour$h and %i minute$i");
	};
	if (($diff->y)>0) {
		$date_diff_f = $diff->format("%y year$y, %m month$m, %d day$d, %h hour$h and %i minute$i");
	};
	
	return $date_diff_f;
	
};

function less_weekends($origin,$date) {

	$now = date("Y-m-d");

	# less weekends
	$weekdays = 0;
	$weekends = 0;
	$start = $origin;
	while (strtotime($start) <= strtotime($now)) {

		if ( (date("D",strtotime($start)) == "Sat") || (date("D",strtotime($start)) == "Sun") ) $weekends++;
		else $weekdays++;

		$start = date("Y-m-d",strtotime("+1 Day",strtotime($start)));

	};
	#
	
	$tdate = date_create($date);
	
	date_sub($tdate,date_interval_create_from_date_string("$weekends days"));

	return date_format($tdate,"Y-m-d H:i:s");

};

function less_weekends_tracks($origin,$date) {

	# less weekends
	$weekdays = 0;
	$weekends = 0;
	$start = $origin;
	while (strtotime($start) <= strtotime($date)) {

		if ( (date("D",strtotime($start)) == "Sat") || (date("D",strtotime($start)) == "Sun") ) $weekends++;
		else $weekdays++;

		$start = date("Y-m-d",strtotime("+1 Day",strtotime($start)));

	};
	#
	
	$tdate = date_create($date);
	
	date_sub($tdate,date_interval_create_from_date_string("$weekends days"));

	return date_format($tdate,"Y-m-d H:i:s");

};

function due_date($con,$id,$setup) {

	$document = $con->getData("SELECT * FROM documents WHERE id = $id");

	$origin = $document[0]['document_date'];
	
	$originating_office = $document[0]['origin'];
	$initial_office = $setup->get_setup_as_string(4);
	
	$received = false;
	$tracks = $con->getData("SELECT * FROM tracks WHERE document_id = ".$document[0]['id']." ORDER BY id DESC");

	# get last system_log from last office where it was received
	foreach ($tracks as $track) {
		
		# if filed no due date
		if ($track['track_action_status']=="filed") {
			
			return "filed";
			exit();
			
		};
		
		if ($track['track_action_status']=="received") {

			$system_log = $track['system_log'];
			$office_id = get_transit_office_id($con,$track['transit']);
			$received = true;
			break;

		};
		
		if ($track['track_action_status']=="picked up") {

			$system_log = $track['system_log'];
			$office_id = get_transit_office_id($con,$track['transit']);
			$received = true;
			break;

		};		

	};

	$transaction = $con->getData("SELECT days FROM transactions WHERE id = ".$document[0]['document_transaction_type']);
	
	$days = $transaction[0]['days'];

	$weekends = 0;
	
	$start = $origin;
	$track_dt = $origin;
	
	if ($received) {
		
		$start = $system_log;
		$track_dt = $system_log;
		
	};
	
	$all_days = date("Y-m-d H:i:s",strtotime("+$days Days",strtotime($start)));	
	
	while (strtotime($start) <= strtotime($all_days)) {

		if ( (date("D",strtotime($start)) == "Sat") || (date("D",strtotime($start)) == "Sun") ) $weekends++;

		$start = date("Y-m-d",strtotime("+1 Day",strtotime($start)));

	};

	$weekdays_only = $days+$weekends;
	$due_date = date("Y-m-d H:i:s",strtotime("+$weekdays_only Day",strtotime($track_dt)));
	
	return $due_date;
	
};

?>