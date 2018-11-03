angular.module('app-module', ['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-document']).factory('app', function($http,$timeout,$compile,$window,bootstrapModal,bui,access,growl) {
	
	function app() {

		var self = this;

		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.views.currentPage = 1;
			
			scope.documents = [];
			
		};
		
		self.view = function(scope,d) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.view)) return;
			
			$window.location.href = "document/view/"+d.id;
			
		};
		
		self.list = function(scope) {

			if (scope.$id > 2) scope = scope.$parent;
		
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 5;
			
			$http({
			  method: 'GET',
			  url: 'api/documents/list'
			}).then(function mySuccess(response) {
				
				scope.documents = response.data;			
				
				scope.filterData = scope.documents;
				scope.currentPage = scope.views.currentPage;

			}, function myError(response) {

			});

		};
		
		self.delete = function(scope,d) {

			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.delete)) return;
		
			var onOk = function() {

				$http({
				  method: 'DELETE',
				  url: 'api/documents/delete/'+d.id,
				}).then(function mySucces(response) {

					self.list(scope);

				}, function myError(response) {

				  // error

				});

			};

			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to delete this document?',onOk,function() {});						

		};		

	};

	return new app();

});