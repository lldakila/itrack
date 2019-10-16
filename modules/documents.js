angular.module('app-module', ['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-document','ngSanitize']).service('myPagination',function($http) {
	
	var self = this;
	
	self.init = function(scope) {		
	
		scope.pageChanged = function() {
            self.getList(scope.pagination.currentPage,scope.pagination.entryLimit).then((response)=>{
				scope.documents = response.data;
          });
        };
		
	};
	
	self.count = function() {

		return $http.post('api/documents/list/0/0');
		
	};
	
	this.getList = function(currentPage, limit) {
		console.log(currentPage);
		offset = (currentPage - 1) * limit;		

		return $http.post('api/documents/list/'+limit+'/'+offset);

	};

	
	/* $http({
	  method: 'GET',
	  url: 'api/documents/list'
	}).then(function mySuccess(response) {

	}, function myError(response) {

	});	 */
	
}).directive('myPagination', function () {
    return {
        restrict: 'A',
        require: 'uibPagination',
        link: function ($scope, $element, $attr, uibPaginationCtrl) {
            uibPaginationCtrl.ShouldHighlightPage = function (pageNum) {
                return true;
            };
        }
    }
}).factory('app', function($http,$timeout,$compile,$window,myPagination,bootstrapModal,bui,access,growl) {
	
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
				count: 0,
                currentPage: 1,
                entryLimit: 25,
                noOfPages: 5				
			};

			/* if (scope.$id > 2) scope = scope.$parent;
		
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

			}); */

			myPagination.count().then((response) => {
                // scope.pagination.count = Math.ceil(response.data.count/scope.pagination.entryLimit);
                scope.pagination.count = response.data.count;
                pagesLinks = [];
                myPagination.getList(scope.pagination.currentPage, scope.pagination.entryLimit).then((response) => {
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