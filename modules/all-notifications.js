angular.module('all-notifications-module', ['ui.bootstrap','bootstrap-modal','block-ui','module-access','form-validator','bootstrap-growl']).factory('allNotifications', function($http,$timeout,$compile,$window,bootstrapModal,bui,access,validate,growl) {
	
	function allNotifications() {
		
		var self = this;
		
		self.load = function(scope) {			
		
			scope.data = {};
			scope.data.notifications = [];
		
			$http({
			  method: 'POST',
			  url: '/api/notifications/fetch',
			  data: scope.filter
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
		
		self.filter = function(scope) {
			
			if (validate.form(scope,'notifications')) {
				
				growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Date is required');
				return;
				
			};
			
			self.load(scope);
			
		};
		
	};
	
	return new allNotifications();
	
});