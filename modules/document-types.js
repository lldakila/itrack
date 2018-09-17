angular.module('app-module', ['bootstrap-modal','ui.bootstrap','module-access','notifications-module','bootstrap-growl']).factory('app', function($http,$timeout,$window,bootstrapModal,growl,access) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};
			
			scope.doc_type = {};
			scope.doc_type.id = 0;
			
			scope.doc_types = [];
			
			scope.staffs = [];
			
			scope.views.currentPage = 1;

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
		
		function staffs() {
			
			$http({
				method: 'GET',
				url: 'api/document-types/staffs'
			}).then(function mySuccess(response) {
				
				scope.staffs = angular.copy(response.data);
					
			}, function myError(response) {
		
			});	
			
		};
		
		self.delete = function(scope, row) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.delete)) return;
			
			scope.views.currentPage = scope.currentPage;
			
			var onOk = function() {
				
				$http({
					method: 'DELETE',
					url: 'api/document-types/delete/'+row.id
				}).then(function mySuccess(response) {
						
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Document Type Information successfully deleted.');
						self.list(scope);
						
				}, function myError(response) {
			

			
				});

			};
			
			var onCancel = function() { };
			
			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to Delete?',onOk,onCancel);
			
		};

		self.list = function(scope) {
			
			if (scope.$id > 2) scope = scope.$parent;
			
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 3;
			
			$http({
			  method: 'GET',
			  url: 'api/document-types/list'
			}).then(function mySuccess(response) {
				
				scope.doc_types = angular.copy(response.data);
				
				scope.filterData = scope.doc_type;
				scope.currentPage = scope.views.currentPage;
				
			}, function myError(response) {
				
			});				
			
		};
		
		self.add = function(scope,doc_type) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add)) return;
			
			var title = 'Add Document Types';
			
			if (doc_type == null) {				
				
				scope.doc_type.staff_assign = [];
				scope.doc_type.staff_assign_dels = [];
			
				scope.doc_type = {};
				scope.doc_type.id = 0;
				
			} else {
				
				
				title = 'Edit Document Type Info';
				
				$http({
				  method: 'GET',
				  url: 'api/document-types/view/'+doc_type.id
				}).then(function mySuccess(response) {
					
					scope.doc_type = angular.copy(response.data);			
					
				}, function myError(response) {
					
					//
					
				});					
				
			};

			var onOk = function() {

				if (validate(scope,'doc_type')){ 
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Some fields are required.');
					return false;
				};		

				var url = 'api/document-types/add';
				var method = 'POST';
				if (scope.doc_type.id != 0) {
					url = 'api/document-types/update';
					method = 'PUT';
				};
				
				$http({
				  method: method,
				  url: url,
				  data: scope.doc_type
				}).then(function mySuccess(response) {				
					
					if (scope.doc_type.id == 0) {
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Document Type Info successfully added.');
					} else{
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Document Type Info successfully updated.');
					}
					self.list(scope);
					
				}, function myError(response) {
					
					//
					
				});
				
				return true;
				
			};
		
			bootstrapModal.box(scope,title,'dialogs/document-type.html',onOk);
			
		};

		self.staff_assign = {
			
			add: function(scope) {

				scope.doc_type.staff_assign.push({
					id: 0,
					staff_name: 0,
				});

			},
			
			edit: function(scope,row) {
				
				row.disabled = !row.disabled;				
				
			},			
			
			delete: function(scope,row) {
				
				if (row.id > 0) {
					scope.doc_type.staff_assign_dels.push(row.id);
				};
				
				var staff_assigns = scope.doc_type.staff_assign;
				var index = scope.doc_type.staff_assign.indexOf(row);
				scope.doc_type.staff_assign = [];			
				
				angular.forEach(staff_assigns, function(d,i) {
					
					if (index != i) {
						
						delete d['$$hashKey'];
						scope.doc_type.staff_assign.push(d);
						
					};
					
				});

			}			
			
		};

	};

	return new app();

});