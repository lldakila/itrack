<?php

class privileges {

	var $system_privileges;
	var $group_privileges;
	var $user_privileges;
	var $page_privileges;

	function __construct($system_privileges,$group_privileges,$user_privileges) {

		$arrayHex = new ArrayHex();
	
		$this->system_privileges = $system_privileges;
		$this->group_privileges = json_decode($arrayHex->toArray($group_privileges),true);
		$this->user_privileges = ($user_privileges==null)?[]:json_decode($arrayHex->toArray($user_privileges),true);		
		
		# sync system privileges with group privileges
		foreach ($this->system_privileges as $key => $system_module) {
			
			if ($this->hasModule($system_module)) {
				
				$this->system_privileges[$key]['privileges'] = $this->getGroupPrivilege($system_module['privileges'],$this->getModule($system_module));
				
			};
			
		};
		
	}

	private function hasModule($system_module) {
		
		$hasModule = false;
		
		foreach ($this->group_privileges as $key => $group_module) {
			
			if ($group_module['id'] == $system_module['id']) $hasModule = true;
			
		};
		
		return $hasModule;

	}
	
	private function getModule($system_module) { # get group module
		
		$getModule = $system_module['privileges'];
		
		foreach ($this->group_privileges as $key => $group_module) {
			
			if ($group_module['id'] == $system_module['id']) $getModule = $group_module['privileges'];
			
		};
		
		return $getModule;		
		
	}
	
	private function getUserModule($system_module) {
		
		$getUserModule = $system_module['privileges'];

		foreach ($this->user_privileges as $key => $user_module) {
			
			if ($user_module['id'] == $system_module['id']) $getUserModule = $user_module['privileges'];
			
		};
		
		return $getUserModule;			
		
	}

	private function getGroupPrivilege($system_privilege,$group_privilege) {

		foreach ($system_privilege as $key => $sp) {
			
			$system_privilege[$key] = $this->checkPrivilegeAccess($group_privilege,$sp);
			
		};
		
		return $system_privilege;
		
	}

	private function checkPrivilegeAccess($group_privilege,$access) {
	
		foreach ($group_privilege as $key => $gp) {
			
			if ($gp['id'] == $access['id']) {

				$access['value'] = $gp['value'];

			}

		}

		return $access;

	}
	
	private function getPagesAccess($system_module_privileges,$group_module_privileges,$group_user_privileges) {
		
		$access = false;
		
		foreach ($system_module_privileges as $key => $smp) {
			
			if ($smp['id'] > 1) continue;
			if (preg_match("/_/",$smp['id'])) continue;			
			
			$access = $this->groupPagesAccess($group_module_privileges,$group_user_privileges,$smp);

		};
		
		return $access;
		
	}
	
	private function getDeleteAccess($system_module_privileges,$group_module_privileges,$group_user_privileges) {
		
		$delete = [];
		
		foreach ($system_module_privileges as $key => $smp) {
			
			if (!preg_match("/delete/",$smp['id'])) continue;
			
			$delete[$smp['id']] = $this->groupPagesAccess($group_module_privileges,$group_user_privileges,$smp);

		};
		
		return $delete;
		
	}	
	
	private function getShowAccess($system_module_privileges,$group_module_privileges,$group_user_privileges) {

		$show = [];

		foreach ($system_module_privileges as $key => $smp) {

			if (!preg_match("/show/",$smp['id'])) continue;

			$show[$smp['id']] = $this->groupPagesAccess($group_module_privileges,$group_user_privileges,$smp);

		};
		
		return $show;

	}	
	
	private function groupPagesAccess($group_privileges,$user_privileges,$page_access) {
		
		$access = false;
		
		foreach ($group_privileges as $key => $gp) {
			
			if (($gp['id'] == $page_access['id']) && ($user_privileges[$key]['id'] == $page_access['id'])) $access = $gp['value'] || $user_privileges[$key]['value'];
			
		};
		
		return $access;
		
	}
	
	public function getPrivileges() {
		
		return $this->system_privileges;
		
	}	
	
	public function getPagesPrivileges() {

		$this->page_privileges = [];

		foreach ($this->system_privileges as $key => $system_module) {
			
			$system_module['value'] = $this->getPagesAccess($system_module['privileges'],$this->getModule($system_module),$this->getUserModule($system_module));
			$system_module['delete'] = $this->getDeleteAccess($system_module['privileges'],$this->getModule($system_module),$this->getUserModule($system_module));		
			$system_module['show'] = $this->getShowAccess($system_module['privileges'],$this->getModule($system_module),$this->getUserModule($system_module));
			
			unset($system_module['privileges']);
			
			$this->page_privileges[] = $system_module;			
			
		};

		$page_privileges = [];
		foreach ($this->page_privileges as $p => $pp) {

			$page_privileges[$pp['id']] = array("value"=>$pp['value'],"delete"=>$pp['delete'],"show"=>$pp['show']);

		};

		return $page_privileges;

	}
	
	public function hasAccess($mod,$prop) {
		
		$group = false;
		$user = false;
		$access = false;
		
		foreach ($this->system_privileges as $sp) {

			if ($sp['id'] == $mod) {

				foreach ($sp['privileges'] as $p) {

					// if ($p['id'] == $prop) $access = $p['value'];
					if ($p['id'] == $prop) $group = $p['value'];

				};

			};

		};
		
		foreach ($this->user_privileges as $up) {
			
			if ($up['id'] == $mod) {

				foreach ($up['privileges'] as $p) {

					// if ($p['id'] == $prop) $access = $p['value'];
					if ($p['id'] == $prop) $user = $p['value'];

				};

			};			
			
		}
		
		$access = $group || $user;
		
		return $access;
	
	}

};

class ArrayHex {
	
	public function toHex($string) {
		
		$string = json_encode($string);
		
		$hex = '';
		for ($i=0; $i<strlen($string); $i++){
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= substr('0'.$hexCode, -2);
		}
		
		return strToUpper($hex);
		
	}

	public function toArray($hex) {
		
		$string='';
		
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		
		return json_decode($string,true);

	}	
	
};



?>