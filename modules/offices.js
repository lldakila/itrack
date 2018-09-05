angular.module('app-module', ['bootstrap-modal','ui.bootstrap','module-access','notifications-module','bootstrap-growl']).factory('app', function($http,$timeout,$window,bootstrapModal,growl,access) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};			
			
			scope.departments = [];
			
			scope.views.currentPage = 1; // for pagination
			
			$http({
				method: 'GET',
				url: 'handlers/departments.php'
			}).then(function mySuccess(response) {
				
				scope.departments = angular.copy(response.data);
					
			}, function myError(response) {
		
			});

			scope.office = {};
			scope.office.id = 0;

			scope.offices = []; // list/table

			

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
			
			scope.views.currentPage = scope.currentPage; // for pagination		
			
			var onOk = function() {
				
				$http({
					method: 'DELETE',
					url: 'api/offices/delete/'+row.id
				}).then(function mySuccess(response) {
					
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Office Information successfully deleted.');
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
			
			/*
			** REFERENCE: https://morgul.github.io/ui-bootstrap4/#!#pagination
			*/

			$http({
			  method: 'GET',
			  url: 'api/offices/list'
			}).then(function mySuccess(response) {
				
				scope.offices = angular.copy(response.data);
				
				scope.filterData = scope.offices; // for pagination
				scope.currentPage = scope.views.currentPage;			
				
			}, function myError(response) {
				
			});				
			
		};
		
		self.add = function(scope,office) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add)) return;
			
			var title = 'Add Office';
			
			if (office == null) {				
				
				scope.office = {};
				scope.office.id = 0;
				
			} else {
				
				title = 'Edit Office Info';
				
				$http({
				  method: 'GET',
				  url: 'api/offices/view/'+office.id
				}).then(function mySuccess(response) {
					
					scope.office = angular.copy(response.data);			
					
				}, function myError(response) {
					
					//
					
				});					
				
			};

			var onOk = function() {

				if (validate(scope,'office')){
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Some fields are required.');
					return false;
				};
				
				var url = 'api/offices/add';
				var method = 'POST';
				if (scope.office.id != 0) {
					url = 'api/offices/update';
					method = 'PUT';
				};
				
				$http({
				  method: method,
				  url: url,
				  data: scope.office
				}).then(function mySuccess(response) {				
					
					if (scope.office.id == 0) {
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Office Info successfully added.');
					} else{
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Office Info successfully updated.');
					}
					self.list(scope);
					
				}, function myError(response) {
					
					//
					
				});
				
				return true;
				
			};
		
			bootstrapModal.box(scope,title,'dialogs/office.html',onOk);
			
		};	

	};

	return new app();

});