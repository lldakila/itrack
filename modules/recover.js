angular.module('app-module', ['form-validator','ui.bootstrap','ngSanitize','bootstrap-growl','block-ui']).factory('app', function($compile,$http,$timeout,validate,growl,validate,bui,$q) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.account = {};

		};
		
		self.recover = function(scope) {
			
			if (validate.form(scope,'recover')) return;
			
			bui.show();
			
			checkEmail(scope).then(function success(response) {
				
				bui.hide();
				sendPassword(scope);
				
			}, function error(response) {

				bui.hide();			
				growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'You have no registered email, please contact your system administrator.');
				
			});
			
			
		};
		
		function checkEmail(scope) {

			return $q(function (resolve,reject) {

				$http({
					method: 'POST',
					url: '/api/login/username',
					data: scope.account
				}).then(function success(response) {
					
					if ((response.data.email_address == null) || (response.data.email_address == "")) reject({status: false});
					else resolve({status: true, email: response.data.email_address});
					
				}, function error(response) {

					reject({status: false});
				
				});

			});

		};
		
		function sendPassword(scope) {
			
			bui.show("Sending email please wait...");
			
			$http({
				method: 'POST',
				url: '/api/login/email',
				data: scope.account
			}).then(function success(response) {
				
				bui.hide();				
				
				if (response.data.status) {
					
					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'You password has been sent to your email address.');					
					
				} else {
					
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Unable to send email, please contact your system administrator.');					
					
				};
				
			}, function error(response) {

				bui.hide();
			
			});			
			
		};

	};

	return new app();

});