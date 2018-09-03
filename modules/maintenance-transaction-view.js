angular.module('app-module', ['bootstrap-modal','module-access','notifications-module']).factory('app', function($http,$timeout,$window,bootstrapModal,access) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};
			
			scope.tran = {};
			scope.tran.id = 0;
			
			scope.trans = [];

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
					url: 'handlers/transaction-delete.php',
					data: {id: row.id}
				}).then(function mySuccess(response) {
					
						self.list(scope);
						
				}, function myError(response) {
			

			
				});

			};
			
			var onCancel = function() { };
			
			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to Delete?',onOk,onCancel);
			
		};

		self.list = function(scope) {
			
			if (scope.$id > 2) scope = scope.$parent;
			
			$http({
			  method: 'GET',
			  url: 'handlers/transaction-list.php'
			}).then(function mySuccess(response) {
				
				scope.trans = angular.copy(response.data);
				
			}, function myError(response) {
				
			});				
			
		};
		
		self.add = function(scope,tran) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add)) return;
			
			var title = 'Add Transaction Types';
			
			if (tran == null) {				
				
				scope.tran = {};
				scope.tran.id = 0;
				
			} else {
				
				title = 'Edit Transaction Type Info';
				
				$http({
				  method: 'POST',
				  url: 'handlers/transaction-view.php',
				  data: {id: tran.id}
				}).then(function mySuccess(response) {
					
					scope.tran = angular.copy(response.data);			
					
				}, function myError(response) {
					
					//
					
				});					
				
			};

			var onOk = function() {

				if (validate(scope,'tran')) return false;				
				
				$http({
				  method: 'POST',
				  url: 'handlers/transaction-save.php',
				  data: scope.tran
				}).then(function mySuccess(response) {				
					
					self.list(scope);
					
				}, function myError(response) {
					
					//
					
				});
				
				return true;
				
			};
		
			bootstrapModal.box(scope,title,'dialogs/transaction.html',onOk);
			
		};	

	};

	return new app();

});