angular.module('module-access', ['bootstrap-growl']).factory('access', function($http,$timeout,$compile,$q,growl) {
	
	function access() {
		
		var self = this;		
		
		self.has = function(scope,group,mod,prop) {
			
			var data = {group: group, mod: mod, prop: prop};
			
			var access = false
			
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'handlers/access.php', false);
			
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			
			xhr.onreadystatechange = function() {
				if(xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
					
					var response = JSON.parse(xhr.responseText);				
					access = response.value;

					if (!response.value) {
						growl.show('danger',{from: 'top', amount: 55},'Sorry, you are not allowed to use this feature.');
						access = response.value;
					};

				} else {

					growl.show('danger',{from: 'top', amount: 55},'Sorry, you are not allowed to use this feature.');
					access = false;

				}
			};
			
			xhr.send(JSON.stringify(data));
			
			return access;
			
		};

	};
	
	return new access();		
	
});