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
		$this->documents = $this->con->getData($sql);

	}

	function new_documents() {

		$documents = $this->documents;
		
		foreach ($documents as $document) {
			
			$tracks = $this->con->getData("SELECT * FROM tracks WHERE document_id = ".$document['id']);
			
			foreach ($tracks as $track) {
				
				// var_dump($this->transit($track));
				var_dump($this->track_action_add_params($track));
				
			};
			
		};

	}
	
	function for_initial() {
		
		$for_initial = 1;
		
		$count = 0;
		
		$documents = $this->documents;

		foreach ($documents as $document) {
			
			$tracks = $this->con->getData("SELECT * FROM tracks WHERE document_id = ".$document['id']);
			
			foreach ($tracks as $track) {
				
				if ($track['track_action'] != $for_initial) continue;
				
				$track_action_add_params = $this->track_action_add_params($track);
				if ( ($track_action_add_params['action_id'] == $for_initial) && (in_array($this->office,$this->get_initial_actors_offices($track_action_add_params))) ) $count++;
				
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
				
				if ($track['track_action'] != $for_approval) continue;
				
				$track_action_add_params = $this->track_action_add_params($track);
				if ( ($track_action_add_params['action_id'] == $for_approval) && (in_array($this->office,$this->get_approval_actors_offices($track_action_add_params))) )  $count++;
				
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
			$no_actors = 0;
			$done = 0;
			
			foreach ($tracks as $track) {					
				
				if ($track['track_action'] == $for_initial) {

					$track_action_add_params = $this->track_action_add_params($track);
					$no_actors = count($this->get_initial_actors_offices($track_action_add_params));
				
				};

				if (in_array($this->office,$this->get_initial_actors_offices($track_action_add_params))) {
					if ($track['track_action_status'] == $initialed) $done++;
				};
				
			};
			
			if ($done == $no_actors) $count++;

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
			$no_actors = 0;
			$done = 0;
			
			foreach ($tracks as $track) {					
				var_dump($track);
				if ($track['track_action'] == $for_approval) {

					$track_action_add_params = $this->track_action_add_params($track);
					$no_actors = count($this->get_approval_actors_offices($track_action_add_params));
				
				};

				/* if (in_array($this->office,$this->get_approval_actors_offices($track_action_add_params))) {
					if ($track['track_action_status'] == $approved) $done++;
				}; */
				
			};
			
			if ($done == $no_actors) $count++;

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