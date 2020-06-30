angular.module('load-js', []).factory('loadjs', function() {
	
	function loadjs() {
		
		var self = this;
		
		self.load = function(url, implementationCode, location = document.body) {
			
			var scriptTag = document.createElement('script');
			scriptTag.src = url;
			
			scriptTag.onload = implementationCode;
			scriptTag.onreadystatechange = implementationCode;
			
			location.appendChild(scriptTag);			
			
		};
		
	};
	
	return new loadjs();
	
});