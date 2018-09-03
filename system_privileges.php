<?php

define('system_privileges', array(
	array(
		"id"=>"dashboard",
		"description"=>"Dashboard",
		"privileges"=>array( # id=1 must be always page access
			array("id"=>1,"description"=>"Show Dashboard","value"=>false),
		),
	),
	array(
		"id"=>"receive_document",
		"description"=>"Receive Document",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Receive Document","value"=>false),
			array("id"=>2,"description"=>"Add Document","value"=>false),
		),
	),
	array(
		"id"=>"incoming",
		"description"=>"Incoming",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Incoming Document","value"=>false),
		),
	),
	array(
		"id"=>"transact",
		"description"=>"Transact",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Transact","value"=>false),
		),
	),
	array(
		"id"=>"outgoing",
		"description"=>"Outgoing",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Outgoing","value"=>false),
		),
	),
	array(
		"id"=>"archives",
		"description"=>"Archives",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Archives","value"=>false),
		),
	),	
	array(
		"id"=>"document_list",
		"description"=>"List of Documents",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show List of Documents","value"=>false),
		),
	),
	array(
		"id"=>"tracks",
		"description"=>"Tracks",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Tracks","value"=>false),
		),
	),
	array(
		"id"=>"accounts",
		"description"=>"Accounts",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show User Accounts","value"=>false),
			array("id"=>2,"description"=>"Add User Account","value"=>false),
			array("id"=>3,"description"=>"Edit User Account","value"=>false),
			array("id"=>4,"description"=>"Delete User Account","value"=>false),
		),
	),
	array(
		"id"=>"groups",
		"description"=>"Groups",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show User Groups","value"=>false),
			array("id"=>2,"description"=>"Add User Groups","value"=>false),
			array("id"=>3,"description"=>"Edit User Groups","value"=>false),
			array("id"=>4,"description"=>"Delete User Groups","value"=>false),
		),
	),
	array(
		"id"=>"maintenance",
		"description"=>"Maintenance",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Maintenance","value"=>false),
			array("id"=>2,"description"=>"Add/Edit Item","value"=>false),
			array("id"=>3,"description"=>"Delete Item","value"=>false),
		),
	),
	array(
		"id"=>"notifications",
		"description"=>"Notifications",
		"privileges"=>array(
			array("id"=>1,"description"=>"Enable Notifications","value"=>false),
		)
	),
));

?>