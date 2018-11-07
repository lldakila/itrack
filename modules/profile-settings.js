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
			
			// info
			scope.settings.info = {};
			
			scope.settings.info.alert = {};
			scope.settings.info.alert.show = false;
			scope.settings.info.alert.message = '';
			
			scope.settings.info.not_unique = false;
			
			// security
			scope.settings.security = {};
			
			scope.settings.security.alert = {};
			
			scope.settings.security.alert.opw = {};
			scope.settings.security.alert.opw.show = false;
			scope.settings.security.alert.opw.message = '';
			scope.settings.security.alert.opw.required = false;
			
			scope.settings.security.alert.pw = {};
			scope.settings.security.alert.pw.show = false;
			scope.settings.security.alert.pw.message = '';
			
			scope.settings.security.alert.opw.correct = false;			
			scope.settings.security.alert.pw.correct = false;		
			scope.settings.security.alert.pw.okMin = false;		
			
			self.info.load(scope);
			
			watchInfo(scope);
			
			watchSecurity(scope);

		};
		
		function watchInfo(scope) {
			
			$timeout(function() {
			
				scope.$watch(function(scope) {
					
					return scope.settings.info.uname;
					
				},function(newValue, oldValue) {
					
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
		
		function watchSecurity(scope) {			
			
			$timeout(function() {			
			
				scope.$watch(function(scope) {
					
					return scope.settings.security.opw;
					
				},function(newValue, oldValue) {

					scope.settings.security.alert.opw.show = false;
					scope.settings.security.alert.opw.message = '';					
				
					opwIsCorrect(scope).then(function(res) {

						if (!scope.settings.btns.security.edit) {
								
							if (!res) {
								
								scope.settings.security.alert.opw.show = true;
								scope.settings.security.alert.opw.message = 'Old password is incorrect';
								scope.settings.security.alert.opw.correct = false;								
								
							} else {
								
								scope.settings.security.alert.opw.correct = true;
								
							};						
							
						};

					}, function(res) {
						
					});

				});
				
				scope.$watch(function(scope) {
					
					return scope.settings.security.pw;
					
				},function(newValue, oldValue) {
					
					pwMatch(scope).then(function(res) {
						
						if (!scope.settings.btns.security.edit) {
						
							if (!res) {
								
								scope.settings.security.alert.pw.show = true;
								scope.settings.security.alert.pw.message = 'New password does not match';
								scope.settings.security.alert.pw.correct = false;
								
							} else {
								
								scope.settings.security.alert.pw.show = false;
								scope.settings.security.alert.pw.message = '';
								scope.settings.security.alert.pw.correct = true;
								
							};
							
						};
						
					}, function(res) {
						
					});
					
				});

				scope.$watch(function(scope) {
					
					return scope.settings.security.rpw;
					
				},function(newValue, oldValue) {
					
					pwMatch(scope).then(function(res) {
						
						if (!scope.settings.btns.security.edit) {
						
							if (!res) {
								
								scope.settings.security.alert.pw.show = true;
								scope.settings.security.alert.pw.message = 'New password does not match';
								scope.settings.security.alert.pw.correct = false;
								
							} else {
								
								scope.settings.security.alert.pw.show = false;
								scope.settings.security.alert.pw.message = '';
								scope.settings.security.alert.pw.correct = true;
								
							};
							
						};
						
					}, function(res) {
						
					});

				});

				scope.$watch(function(scope) {
					
					return scope.settings.security.alert.pw.correct;
					
				},function(newValue, oldValue) {
					
					if (scope.settings.security.alert.pw.correct) {
						
						if (belowMinChar(scope.settings.security.pw,6)) {

							scope.settings.security.alert.pw.show = true;
							scope.settings.security.alert.pw.message = 'Password must be at least 6 characters';
							scope.settings.security.alert.pw.okMin = false;					

						} else {

							scope.settings.security.alert.pw.show = false;
							scope.settings.security.alert.pw.message = '';
							scope.settings.security.alert.pw.okMin = true;					

						};
						
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
				
				scope.settings.info.alert = {};
				scope.settings.info.alert.show = false;
				scope.settings.info.alert.message = '';
				
				scope.settings.info.not_unique = false;				
				
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
				
				delete scope.settings.security.opw;
				delete scope.settings.security.pw;
				delete scope.settings.security.rpw;
				
				scope.formHolder.security.opw.$touched = false;
				scope.formHolder.security.pw.$touched = false;
				scope.formHolder.security.rpw.$touched = false;
				
				scope.settings.security.alert.opw.show = false;
				scope.settings.security.alert.opw.message = '';
				scope.settings.security.alert.opw.required = false;
				
				scope.settings.security.alert.pw.show = false;
				scope.settings.security.alert.pw.message = '';

				scope.settings.security.alert.opw.correct = false;			
				scope.settings.security.alert.pw.correct = false;

				scope.settings.security.alert.pw.okMin = false;				
				
			};

			scope.settings.btns.security.edit = !scope.settings.btns.security.edit;

		};
		
		self.security.update = function(scope) {
			
			if (!scope.settings.security.alert.opw.correct) {
				
				scope.settings.security.alert.opw.show = true;
				scope.settings.security.alert.opw.message = 'Old password is incorrect';				
				
			} else {
				
				scope.settings.security.alert.opw.show = false;
				scope.settings.security.alert.opw.message = '';				
				
			};
			
			if (!validate(scope,'security') && scope.settings.security.alert.opw.correct && scope.settings.security.alert.pw.correct && scope.settings.security.alert.pw.okMin) {
				
				bui.show();
				
				$http({
					method: 'POST',
					url: 'api/profile/update/security',
					data: scope.settings.security
				}).then(function mySuccess(response) {
	
					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Your password has been updated. Please logout then login again.');						
					scope.settings.btns.security.edit = true;
					bui.hide();
		
				}, function myError(response) {

					bui.hide();

				});
				
			};
			
		};
		
		function opwIsCorrect(scope) {
			
			return $q(function(resolve,reject) {
				
				$http({
					method: 'GET',
					url: 'api/profile/security',
				}).then(function mySuccess(response) {

					resolve(response.data.pw===scope.settings.security.opw);
		
				}, function myError(response) {

					reject(false);

				});					
				
			});
			
		};
		
		function pwMatch(scope) {
			
			return $q(function(resolve,reject) {
				
				var match = scope.settings.security.pw == scope.settings.security.rpw;
				
				resolve(match);
				if (!match) resolve(false);
				
			});	
			
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