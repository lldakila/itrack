angular.module('all-notifications-module', ['ui.bootstrap','bootstrap-modal','block-ui','module-access']).factory('allNotifications', function($http,$timeout,$compile,$window,bootstrapModal,bui,access) {
	
	function allNotifications() {
		
		var self = this;
		
		self.load = function(scope) {			
		
			scope.data = {};
			scope.data.notifications = [];
		
			$http({
			  method: 'GET',
			  url: '/api/notifications/fetch'
			}).then(function mySucces(response) {

				scope.data.notifications = angular.copy(response.data);

			}, function myError(response) {

			});	

		};
		
		self.hide = function(scope,id) {
			
			$http({
			  method: 'GET',
			  url: '/api/notifications/hide/'+id,
			}).then(function mySucces(response) {
				
				if (scope.$id > 2) scope = scope.$parent;
				
				self.load(scope);
			
			}, function myError(response) {
				
				
			});
			
		};
		
	};
	
	return new allNotifications();
	
});