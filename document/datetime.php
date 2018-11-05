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

function due_date($origin,$days) {

	$all_days = date("Y-m-d H:i:s",strtotime("+$days Days",strtotime($origin)));

	$weekends = 0;
	$start = $origin;
	while (strtotime($start) <= strtotime($all_days)) {

		if ( (date("D",strtotime($start)) == "Sat") || (date("D",strtotime($start)) == "Sun") ) $weekends++;

		$start = date("Y-m-d",strtotime("+1 Day",strtotime($start)));

	};
	#

	$weekdays_only = $days+$weekends;
	$due_date = date("Y-m-d H:i:s",strtotime("+$weekdays_only Day",strtotime($origin)));
	
	return $due_date;
	
};

?>