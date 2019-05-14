<?php

define('system_setup', array(
	array(
		"id"=>1,
		"description"=>"Users for document action: initial at receiving document",
		"values"=>[2,8,9],
	),
	array(
		"id"=>2,
		"description"=>"Users for document action: signature at receiving document",
		"values"=>[2,6,9],
	),	
	array(
		"id"=>3,
		"description"=>"Users for document action: route at receiving document",
		"values"=>[8],
	),
	array(
		"id"=>4,
		"description"=>"Office for document action",
		"values"=>[2],
	),
	array(
		"id"=>5,
		"description"=>"Liaison officers",
		"values"=>[4],
	),
	array(
		"id"=>6,
		"description"=>"Exclude from selections/autosuggest",
		"values"=>[2,6,8],
	),
	array(
		"id"=>7,
		"description"=>"Admin officers",
		"values"=>[6],
	),	
	array(
		"id"=>8,
		"description"=>"Admin assistants",
		"values"=>[8],
	),
	array(
		"id"=>9,
		"description"=>"Admin aides",
		"values"=>[9],
	),
	array(
		"id"=>10,
		"description"=>"Liaisons AOs AAsts AAs",
		"values"=>[4,6,8,9],
	),
	array(
		"id"=>11,
		"description"=>"PA Staffs",
		"values"=>[3],
	),
	array(
		"id"=>12,
		"description"=>"OPG Office",
		"values"=>3,
	),
	array(
		"id"=>13,
		"description"=>"OPA Office",
		"values"=>2,
	)	
));

class setup {

	var $system_setup;

	function __construct($system_setup) {
		
		$this->system_setup = $system_setup;
		
	}
	
	function get_setup($id) {
		
		$values = array();
		
		foreach ($this->system_setup as $setup) {
			
			if ($setup['id'] == $id) $values = $setup['values'];
			
		};
		
		return $values;
		
	}

	function get_setup_as_string($id) {

		$values = array();
		$values_string = "";

		foreach ($this->system_setup as $setup) {

			if ($setup['id'] == $id) $values = $setup['values'];

		};

		$values_string = implode(",",$values);

		return $values_string;

	}

};

?>