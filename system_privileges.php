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
		"description"=>"Add Document",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Add Document","value"=>false),
			array("id"=>2,"description"=>"Add Document","value"=>false),
		),
	),
	array(
		"id"=>"receive",
		"description"=>"Receive Document",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Receive Document","value"=>false),
			array("id"=>2,"description"=>"Receive Document","value"=>false),
		),
	),
	array(
		"id"=>"file_document",
		"description"=>"File Document",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show File Document","value"=>false),
			array("id"=>2,"description"=>"File Document","value"=>false),
		),
	),	
	array(
		"id"=>"update_tracks",
		"description"=>"Update Tracks",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Update Tracks","value"=>false),
			array("id"=>2,"description"=>"View Document","value"=>false),
			array("id"=>3,"description"=>"Update Document Tracks","value"=>false),
			array("id"=>4,"description"=>"Transit Document","value"=>false),
			array("id"=>5,"description"=>"Release Document","value"=>false),
			array("id"=>6,"description"=>"Add Comment","value"=>false),	
			array("id"=>7,"description"=>"Add Revision","value"=>false),	
			array("id"=>8,"description"=>"Edit Revision","value"=>false),
			array("id"=>"delete_revision","description"=>"Delete Revision","value"=>false),
		),
	),
	array(
		"id"=>"track_document",
		"description"=>"Track Document",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Track Document","value"=>false),
			array("id"=>2,"description"=>"Track Document","value"=>false),
		),
	),	
	array(
		"id"=>"documents",
		"description"=>"List of Documents",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Documents","value"=>false),
			array("id"=>2,"description"=>"View Document","value"=>false),
			array("id"=>3,"description"=>"Edit Document","value"=>false),
			array("id"=>"delete_document","description"=>"Delete Document","value"=>false),
		),
	),
	array(
		"id"=>"accounts",
		"description"=>"Accounts",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show User Accounts","value"=>false),
			array("id"=>2,"description"=>"Add User Account","value"=>false),
			array("id"=>3,"description"=>"Edit User Account","value"=>false),
			array("id"=>"delete_account","description"=>"Delete User Account","value"=>false),
		),
	),
	array(
		"id"=>"groups",
		"description"=>"Groups",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show User Groups","value"=>false),
			array("id"=>2,"description"=>"Add User Groups","value"=>false),
			array("id"=>3,"description"=>"Edit User Groups","value"=>false),
			array("id"=>"delete_group","description"=>"Delete User Groups","value"=>false),
		),
	),
	array(
		"id"=>"maintenance",
		"description"=>"Maintenance",
		"privileges"=>array(
			array("id"=>1,"description"=>"Show Maintenance","value"=>false),
			array("id"=>2,"description"=>"Add/Edit Item","value"=>false),
			array("id"=>"delete_item","description"=>"Delete Item","value"=>false),
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