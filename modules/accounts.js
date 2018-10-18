angular.module('app-module', ['form-validator','bootstrap-modal','ui.bootstrap','ngRoute','module-access','notifications-module','bootstrap-growl']).config(function($routeProvider) {
    $routeProvider
        .when('/:option/:id', {
            templateUrl: 'account-add.html'
        })
        .when('/:option/:id', {
            templateUrl: 'account-add.html'
        });		
}).factory('app', function($http,$timeout,$window,$routeParams,$location,validate,bootstrapModal,growl,access) {
	
	function app() {

		var self = this;

		self.startup = function(scope) {
			
			scope.controls.add = true;
			scope.controls.edit = false;	
			
			scope.$on('$routeChangeSuccess', function() {
				
				switch ($routeParams.option) {
					
					case 'view':
					
						if ($routeParams.id != undefined) {					
							self.load(scope,$routeParams.id);
							scope.controls.add = false;
							scope.controls.edit = true;
						};					
					
					break;
					
					case 'delete':
					
						if ($routeParams.id != undefined) {					
							self.load(scope,$routeParams.id);
							scope.controls.add = false;
							scope.controls.edit = false;
							scope.controls.ok=false;
							scope.controls.cancel=false;
							self.deleteConfirm(scope,$routeParams.id);
						};
							
					break;
					
				};				

			});				
			
		};
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.views.currentPage = 1;
			
			scope.controls = {
				btns: {
					ok: true,
					cancel: true
				},
				add: true,
				edit: true,
				ok: true,
				cancel:true
			};
			
			scope.user = {};
			
			scope.user.id = 0;	
			
			scope.users = [];
			
			scope.offices = [];
			
			scope.groups = [];
			

			scope.views.currentPage = 1;

			
			$http({
				method: 'GET',
				url: 'handlers/offices.php'
			}).then(function mySuccess(response) {
				
				scope.offices = angular.copy(response.data);
					
			}, function myError(response) {
		
		
		
			});			
			$http({
				method: 'GET',
				url: 'handlers/groups.php'
			}).then(function mySuccess(response) {
				
				scope.groups = angular.copy(response.data);
					
			}, function myError(response) {
		
		
		
			});			

		};
		
		self.add = function(scope) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add)) return;
			
			scope.controls.btns.ok = false;
			scope.controls.btns.cancel = false;
			
		};
		
		self.cancel = function(scope) {
			
			scope.controls.btns.ok = true;
			scope.controls.btns.cancel = true;
			
			validate.cancel(scope,'user');
			
			$timeout(function() {
				if ($routeParams.option==undefined) scope.user = {};				
			},500);
			
		};
		
		self.view = function(scope,row) {
			
			$window.location.href = "account-add.html#!/view/"+row.id;
			
		};

		self.delete = function(scope,row){
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.delete)) return;
			
			scope.views.currentPage = scope.currentPage;
			
			$window.location.href = "account-add.html#!/delete/"+row.id;
			
		};
		
		self.deleteConfirm = function(scope,id) {
			
			var onOk = function() {
				
				$http({
					method: 'DELETE',
					url: 'api/accounts/delete/'+id,
				}).then(function mySuccess(response) {
					
						$window.location.href = "account-list.html";

				}, function myError(response) {
			
				});

			};
			
			var onCancel = function() {
				
				$window.location.href = "account-list.html";
				
			};
			
			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to Delete?',onOk,onCancel);
				
		};
		
		self.edit = function(scope) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.edit)) return;
			
			scope.controls.btns.ok = false;
			scope.controls.btns.cancel = false;			
			
		};
		
		self.load = function(scope,id) {
			
			$http({
			  method: 'GET',
			  url: 'api/accounts/view/'+id,
			}).then(function mySuccess(response) {
				
				scope.user = angular.copy(response.data);			
				
			}, function myError(response) {
				
				//
				
			});			
			
		};
		
		self.save = function(scope) {

			// validation
			if (validate.form(scope,'user')){ 
			growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 55},'Some fields are required.');
			return;
			}
			var url = 'api/accounts/add';
			var method = 'POST';
			if (scope.user.id != 0) {
				url = 'api/accounts/update';
				method = 'PUT';
			};
			
			$http({
			  method: method,
			  url: url,
			  data: scope.user
			}).then(function mySuccess(response) {
				
				if (scope.user.id == 0) {
					scope.user = {};
					scope.user.id = 0;
					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Account Info successfully added.');
				} else{
					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Account Info successfully updated.');
				};
				scope.controls.btns.ok = true;
				scope.controls.btns.cancel = true;					
				
			}, function myError(response) {
				
				//
				
			});			
			
		};
		
		self.list = function(scope) {
			
			scope.currentPage = scope.views.currentPage; // for pagination
			scope.pageSize = 10; // for pagination
			scope.maxSize = 3; // for pagination

			$http({
			  method: 'GET',
			  url: 'api/accounts/list'
			}).then(function mySuccess(response) {
				
				scope.users = angular.copy(response.data);	

				scope.filterData = scope.users; // for pagination
				scope.currentPage = scope.views.currentPage; // for pagination 							

				
			}, function myError(response) {
				
				//
				
			});				
			
		};

	};

	return new app();

});