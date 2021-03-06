<?php

class dashboard_counters {
	
	var $con;
	var $office;
	var $filters;
	var $selected;
	var $documents;
	
	function __construct($con,$filter,$office) {
		
		$this->con = $con;
		$this->office = $office;
		
		$this->selected = $filter['selected']['period'];		
		
		$filters = array(
			"documents"=>array(
				"date"=>"",
				"week"=>"",
				"month"=>"",
				"year"=>""
			),
			"tracks"=>array(
				"date"=>"",
				"week"=>"",
				"month"=>"",
				"year"=>""
			),		
		);
		
		switch ($this->selected) {

			case 'date':

				$filters['documents'][$this->selected] = "document_date LIKE '".date("Y-m-d",strtotime($filter['date']))."%'";
				$filters['tracks'][$this->selected] = "system_log LIKE '".date("Y-m-d",strtotime($filter['date']))."%'";

			break;

			case 'week':

				$filters['documents'][$this->selected] = "document_date BETWEEN '".date("Y-m-d",strtotime($filter['week']['from']))."' AND '".date("Y-m-d",strtotime($filter['week']['to']))."'";
				$filters['tracks'][$this->selected] = "system_log BETWEEN '".date("Y-m-d",strtotime($filter['week']['from']))."' AND '".date("Y-m-d",strtotime($filter['week']['to']))."'";

			break;

			case 'month':
			
				$filters['documents'][$this->selected] = "document_date LIKE '".$filter['year']."-".$filter['month']['month']."-%'";
				$filters['tracks'][$this->selected] = "system_log LIKE '".$filter['year']."-".$filter['month']['month']."-%'";
			
			break;
			
			case 'year':
			
				$filters['documents'][$this->selected] = "document_date LIKE '".$filter['year']."-%'";
				$filters['tracks'][$this->selected] = "system_log LIKE '".$filter['year']."-%'";
			
			break;
			
		};

		$this->filters = $filters;

		$sql = "SELECT * FROM documents WHERE ".$this->filters['documents'][$this->selected];
		$documents = $this->con->getData($sql);
		
		$this->documents = [];
		foreach ($documents as $document) {
			
			if (is_filed($con,$document['id'])) continue;
			$this->documents[] = $document;
			
		};

	}

	function new_documents() {

		$documents = $this->documents;
		
		$count = 0;
		foreach ($documents as $document) {

			$count++;
			
		};

		return $count;

	}
	
	function for_initial() {
		
		$for_initial = 1;
		
		$count = 0;
		
		$documents = $this->documents;

		foreach ($documents as $document) {
			
			$tracks = $this->con->getData("SELECT * FROM tracks WHERE document_id = ".$document['id']);
			
			foreach ($tracks as $track) {
				
				if (($track['track_action'] == $for_initial) && ($track['office_id'] == $this->office)) {
					$count++;
					break;
				}
				
			};
			
		};
		
		return $count;
		
	}
	
	function for_approval() {
		
		$for_approval = 2;
		
		$count = 0;
		
		$documents = $this->documents;

		foreach ($documents as $document) {
			
			$tracks = $this->con->getData("SELECT * FROM tracks WHERE document_id = ".$document['id']);
			
			foreach ($tracks as $track) {
				
				if (($track['track_action'] == $for_approval) && ($track['office_id'] == $this->office)) {
					$count++;
					break;
				}
				
			};
			
		};
		
		return $count;		
		
	}
	
	function initialed() {
		
		$for_initial = 1;		
		$initialed = "initialed";
		
		$count = 0;
		
		$documents = $this->documents;

		foreach ($documents as $document) {
			
			$tracks = $this->con->getData("SELECT * FROM tracks WHERE document_id = ".$document['id']);
			
			foreach ($tracks as $track) {				
				
				if (($track['track_action_status'] == $initialed) && ($track['office_id']==$this->office)) {
					$count++;
					break;
				}
				
			};

		};
		
		return $count;		
		
	}
	
	function approved() {
		
		$for_approval = 2;		
		$approved = "approved";
		
		$count = 0;
		
		$documents = $this->documents;

		foreach ($documents as $document) {
			
			$tracks = $this->con->getData("SELECT * FROM tracks WHERE document_id = ".$document['id']);
			
			foreach ($tracks as $track) {					

				if (($track['track_action_status'] == $approved) && ($track['office_id']==$this->office)) {
					$count++;
					break;
				}
				
			};

		};
		
		return $count;		
		
	}
	
	function incoming() {
		
		$office = $this->office;
		
		$incoming = 0;
		
		foreach ($this->documents as $document) {
			
			// if ($document['origin'] == $office) $incoming++;
			
		}
		
		return $incoming;
		
	}
	
	function outgoing() {
		
		$office = $this->office;
		
		$outgoing = 0;

		foreach ($this->documents as $document) {
			
			if ($document['origin'] == $office) $outgoing++;
			
		}

		return $outgoing;

	}
	
	function transit($track) {
		
		return json_decode($track['transit'],true);
		
	}
	
	function get_transit_office($transit) {
		
		return intval($transit['office']);
		
	}
	
	function track_action_add_params($track) {
		
		return json_decode($track['track_action_add_params'],true);
		
	}
	
	function get_initial_actors_offices($track_action_add_params) {
		
		$actors_offices = [];
		
		if ($track_action_add_params['action_id'] == 1) {
			
			foreach ($track_action_add_params['options'] as $option) {
				
				if ($option['value']) $actors_offices[] = $option['office']['id'];

			};

		};
	
		return $actors_offices;
	
	}
	
	function get_approval_actors_offices($track_action_add_params) {
		
		$actors_offices = [];
		
		if ($track_action_add_params['action_id'] == 2) {
			
			foreach ($track_action_add_params['options'] as $option) {
				
				if ($option['value']) $actors_offices[] = $option['office']['id'];

			};

		};
	
		return $actors_offices;
	
	}	
	
}

?>