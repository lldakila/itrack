<?php

define('system_setup', array(
	array(
		"id"=>1,
		"description"=>"Users for document action: initial/signature at receiving document",
		"values"=>[2,8],
	),
	array(
		"id"=>2,
		"description"=>"Users for document action: route at receiving document",
		"values"=>[8],
	),
	array(
		"id"=>3,
		"description"=>"Office for document action",
		"values"=>[2],
	),	
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