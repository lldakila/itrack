angular.module('app-module', ['bootstrap-modal','ui.bootstrap','module-access','notifications-module','bootstrap-growl']).factory('app', function($http,$timeout,$window,bootstrapModal,growl,access) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};
			
			scope.views.currentPage = 1;
			
			scope.option = {};
			scope.option.id = 0;
			
			scope.options = []; //list

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
					url: 'api/options/delete/'+row.id
				}).then(function mySuccess(response) {
						
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Option Info successfully deleted.');
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
			  url: 'api/options/list'
			}).then(function mySuccess(response) {
				
				scope.options = angular.copy(response.data);
				
				scope.filterData = scope.options; // for pagination
				scope.currentPage = scope.views.currentPage;
				
			}, function myError(response) {
				
			});				
			
		};
		
		self.add = function(scope,option) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add)) return;
			
			var title = 'Add Option Types';
			
			if (option == null) {				
				
				scope.option = {};
				scope.option.id = 0;
				
			} else {
				
				title = 'Edit Option Type Info';
				
				$http({
				  method: 'GET',
				  url: 'api/options/view/'+option.id
				}).then(function mySuccess(response) {
					
					scope.option = angular.copy(response.data);			
					
				}, function myError(response) {
					
					//
					
				});					
				
			};

			var onOk = function() {

				if (validate(scope,'option')) {
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Some fields are required.');
					return false;
					};
				
				var url = 'api/options/add';
				var method = 'POST';
				if (scope.option.id != 0) {
					url = 'api/options/update';
					method = 'PUT';
				};
				
				$http({
				  method: method,
				  url: url,
				  data: scope.option
				}).then(function mySuccess(response) {				
					
					if (scope.option.id == 0) {
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Option Info successfully added.');
					} else{
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Option Info successfully updated.');
					}
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