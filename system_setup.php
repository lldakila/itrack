<?php

define('system_setup', array(
	array(
		"id"=>1,
		"description"=>"Admin Groups",
		"values"=>array(
			array("id"=>1,"value"=>2),
			array("id"=>2,"value"=>3),
			array("id"=>3,"value"=>7),
		),
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
			
			if ($setup['id'] == $id) $values = $setup;
			
		};
		
		return $values;
		
	}

	function get_setup_as_string($id) {

		$values = array();
		$values_string = "";

		foreach ($this->system_setup as $setup) {
			
			if ($setup['id'] == $id) $values = $setup['values'];
			
		};

		foreach ($values as $i => $value) {

			if ($i == (count($values)-1)) $values_string = $values_string.$value['value'];
			else $values_string = $values_string.$value['value'].",";

		};

		return $values_string;

	}

};

?>