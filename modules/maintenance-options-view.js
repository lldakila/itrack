angular.module('app-module', ['bootstrap-modal','module-access','notifications-module']).factory('app', function($http,$timeout,$window,bootstrapModal,access) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};
			
			scope.opt = {};
			scope.opt.id = 0;
			
			scope.opts = [];

		};
		
		function validate(scope,form) {
			
			var controls = scope.formHolder[form].$$controls;
			
			angular.forEach(controls,function(elem,i) {

				if (elem.$$attr.$attr.required) {
					
					scope.$apply(function() {
						
						elem.$touched = elem.$invalid;
						
					});
					
				};
									
			});

			return scope.formHolder[form].$invalid;
			
		};
		
		self.delete = function(scope, row) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.delete)) return;
			
			var onOk = function() {
				
				$http({
					method: 'POST',
					url: 'handlers/options-delete.php',
					data: {id: row.id}
				}).then(function mySuccess(response) {
					
						self.list(scope);
						
				}, function myError(response) {
			

			
				});

			};
			
			var onCancel = function() { };
			
			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to delete this option?',onOk,onCancel);
			
		};

		self.list = function(scope) {
			
			if (scope.$id > 2) scope = scope.$parent;
			
			$http({
			  method: 'GET',
			  url: 'handlers/options-list.php'
			}).then(function mySuccess(response) {
				
				scope.opts = angular.copy(response.data);
				
			}, function myError(response) {
				
			});				
			
		};
		
		self.add = function(scope,opt) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add)) return;
			
			var title = 'Add Option';
			
			if (opt == null) {				
				
				scope.opt = {};
				scope.opt.id = 0;
				
			} else {
				
				title = 'Edit Option Info';
				
				$http({
				  method: 'POST',
				  url: 'handlers/options-view.php',
				  data: {id: opt.id}
				}).then(function mySuccess(response) {
					
					scope.opt = angular.copy(response.data);			
					
				}, function myError(response) {
					
					//
					
				});					
				
			};

			var onOk = function() {

				if (validate(scope,'opt')) return false;				
				
				$http({
				  method: 'POST',
				  url: 'handlers/options-save.php',
				  data: scope.opt
				}).then(function mySuccess(response) {				
					
					self.list(scope);
					
				}, function myError(response) {
					
					//
					
				});
				
				return true;
				
			};
		
			bootstrapModal.box(scope,title,'dialogs/option.html',onOk);
			
		};	

	};

	return new app();

});