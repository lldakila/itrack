angular.module('app-module', ['form-validator','ui.bootstrap','ngSanitize','bootstrap-growl']).factory('app', function($compile,$http,$timeout,validate,growl) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.account = {};

		};
		
	};

	return new app();

});