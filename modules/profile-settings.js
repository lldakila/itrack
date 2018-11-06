angular.module('app-module', ['bootstrap-modal','ui.bootstrap','module-access','notifications-module','bootstrap-growl','block-ui']).factory('app', function($http,$timeout,$window,bootstrapModal,growl,access,$q,bui) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {
			
			scope.formHolder = {};
			
			scope.settings = {};
			scope.settings.btns = {};
			
			scope.settings.btns.info = {
				edit: true,
			};			
			
			scope.settings.btns.security = {
				edit: true,
			};
			
			scope.settings.info = {};
			
			scope.settings.info.alert = {};
			scope.settings.info.alert.show = false;
			scope.settings.info.alert.message = '';
			
			scope.settings.info.not_unique = false;
			
			scope.settings.security = {};
			
			self.info.load(scope);
			
			$timeout(function() {
			
				scope.$watch(function(scope) {
					
					return scope.settings.info.uname;
					
				},function(newValue, oldValue) {
					
					scope.settings.info.uname = newValue;
					
					username_is_unique(scope).then(function(res) {
						
						scope.settings.info.not_unique = res;
						
					}, function(res) {
						
					});

					scope.settings.info.alert.show = false;
					scope.settings.info.alert.message = '';

					if (belowMinChar(newValue,2)) {

						scope.settings.info.alert.show = true;
						scope.settings.info.alert.message = 'Username must be at least 2 characters';
						
					};
					
					if (hasSpace(newValue)) {
						
						scope.settings.info.alert.show = true;
						scope.settings.info.alert.message = 'Space is not allowed';						
						
					};
					
				});

			}, 1000);

		};
		
		function validate(scope,form) {
			
			var controls = scope.formHolder[form].$$controls;
			
			angular.forEach(controls,function(elem,i) {

				if (elem.$$attr.$attr.required) {
						
					elem.$touched = elem.$invalid;
					
				};
									
			});

			return scope.formHolder[form].$invalid;
			
		};
		
		self.info = {};
		
		self.info.load = function(scope) {

			$http({
				method: 'GET',
				url: 'api/profile/info',
			}).then(function mySuccess(response) {

				scope.settings.info.uname = response.data.uname;
	
			}, function myError(response) {

			});			

		};		
		
		self.security = {};		
		
		self.security.load = function(scope) {

			$http({
				method: 'GET',
				url: 'api/profile/security',
			}).then(function mySuccess(response) {


	
			}, function myError(response) {



			});			

		};
		
		self.info.edit = function(scope) {

			if (!scope.settings.btns.info.edit) {
				
				self.info.load(scope);
				
			};

			scope.settings.btns.info.edit = !scope.settings.btns.info.edit;

		};		
		
		self.info.update = function(scope) {

			if (validate(scope,'info')) return;
			
			username_is_unique(scope).then(function(res) {
				
				scope.settings.info.not_unique = res;
				
				if (!scope.settings.info.not_unique) {
					
					bui.show();
					
					$http({
						method: 'POST',
						url: 'api/profile/update/info',
						data: scope.settings.info
					}).then(function mySuccess(response) {
		
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Your username has been updated.');						
						scope.settings.btns.info.edit = true;
						bui.hide();
			
					}, function myError(response) {

						bui.hide();

					});						
					
				};
				
			}, function(res) {
				
			});
			
		};		
		
		function username_is_unique(scope) {
			
			return $q(function(resolve,reject) {
				
				$http({
					method: 'POST',
					url: 'api/profile/username',
					data: scope.settings.info
				}).then(function mySuccess(response) {

					resolve(response.data.status);
		
				}, function myError(response) {

					reject(false);

				});					
				
			});
			
		};
		
		self.security.edit = function(scope) {

			if (!scope.settings.btns.security.edit) {
				
			};

			scope.settings.btns.security.edit = !scope.settings.btns.security.edit;

		};
		
		self.security.update = function(scope) {
			
			
			
		};
		
		function belowMinChar(str,min) {
			
			return str.length < min;
			
		};
		
		function hasSpace(str) {
			
			return /\s/.test(str);
			
		};

	};

	return new app();

});