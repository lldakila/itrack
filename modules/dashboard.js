angular.module('dashboard-module', ['ui.bootstrap','bootstrap-modal','block-ui','module-access']).factory('dashboard', function($http,$timeout,$compile,$window,bootstrapModal,bui,access) {
	
	function dashboard() {
		
		var self = this;
		
		self.load = function(scope) {
			
			$http({
				method: 'GET',
				url: 'handlers/dashboard.php'				
			}).then(function succes(response) {
				
				scope.documents = response.data;
				
			}, function error(response) {
				
			});
			
		};
		
	};
	
	return new dashboard();
	
});