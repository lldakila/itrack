angular.module('app-module', ['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-document','ngSanitize','my-pagination']).factory('app', function($http,$timeout,$compile,$window,myPagination,bootstrapModal,bui,access,growl) {
	
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

			myPagination.init(scope);

            scope.orderByAttribute = 'id';
            scope.sortReverse = false; // set the default sort order
			scope.pagination = {
				url: 'api/documents/list/',
				count: 0,
                currentPage: 1,
                entryLimit: 25,
                noOfPages: 5				
			};

			let filters = {};
			myPagination.count(scope.pagination.url+'0/1',filters).then((response) => {
                scope.pagination.count = response.data.count;
                pagesLinks = [];
                myPagination.getList(scope.pagination.url+scope.pagination.entryLimit+'/'+scope.pagination.currentPage, filters).then((response) => {
					scope.documents = response.data;
                });
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