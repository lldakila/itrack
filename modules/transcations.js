angular.module('app-module', ['bootstrap-modal','ui.bootstrap','module-access','notifications-module','bootstrap-growl']).factory('app', function($http,$timeout,$window,bootstrapModal,growl,access) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};
			
			scope.views.currentPage = 1;
			
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
					method: 'DELETE',
					url: 'api/transactions/delete/'+row.id
				}).then(function mySuccess(response) {
						
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Transaction Info successfully deleted.');
						self.list(scope);
						
				}, function myError(response) {
			

				});

			};
			
			var onCancel = function() { };
			
			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to Delete?',onOk,onCancel);
			
		};

		self.list = function(scope) {
			
			if (scope.$id > 2) scope = scope.$parent;
			
			scope.currentPage = scope.views.currentPage; // for pagination
			scope.pageSize = 10;
			scope.maxSize = 3;
			
			$http({
			  method: 'GET',
			  url: 'api/transactions/list'
			}).then(function mySuccess(response) {
				
				scope.trans = angular.copy(response.data);
				
				scope.filterData = scope.trans; // for pagination
				scope.currentPage = scope.views.currentPage;	
				
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
				  method: 'GET',
				  url: 'api/transactions/view/'+tran.id
				}).then(function mySuccess(response) {
					
					scope.tran = angular.copy(response.data);			
					
				}, function myError(response) {
					
					//
					
				});					
				
			};

			var onOk = function() {

				if (validate(scope,'tran')) {
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Some fields are required.');
					return false;
					};
				
				var url = 'api/transactions/add';
				var method = 'POST';
				if (scope.tran.id != 0) {
					url = 'api/transactions/update';
					method = 'PUT';
				};
				
				$http({
				  method: method,
				  url: url,
				  data: scope.tran
				}).then(function mySuccess(response) {				
					
					if (scope.tran.id == 0) {
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Transaction Info successfully added.');
					} else{
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Transaction Info successfully updated.');
					}
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