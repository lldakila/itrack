angular.module('app-url', []).factory('url', function() {
	
	function url() {
		
		var self = this;
		
		self.init = function(scope) {
			
			scope.url = '../../../';
			
		};
		
	};
	
	return new url();
	
});