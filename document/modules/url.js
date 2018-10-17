angular.module('app-url', []).factory('url', function() {
	
	function url() {
		
		var self = this;
		
		self.init = function(scope) {
			
			scope.url = {
				view: '../../',
				for: '../../../'
			};
			
		};
		
	};
	
	return new url();
	
});