angular.module('dashboard-module', ['ui.bootstrap','bootstrap-modal','block-ui','module-access']).factory('dashboard', function($http,$timeout,$compile,$window,bootstrapModal,bui,access) {
	
	function dashboard() {
		
		var self = this;
		
		self.load = function(scope) {
			
			scope.formHolder = {};
			
			scope.filter = {};
			scope.filter.period = {};
			scope.periods = [
				{period: "date", text: "Daily"},
				{period: "week", text: "Weekly"},
				{period: "month", text: "Monthly"},
				{period: "year", text: "Annually"},
			];
			
		};
		
	};
	
	return new dashboard();
	
});