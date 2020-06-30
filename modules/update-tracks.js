angular.module('app-module', ['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-action','my-pagination']).factory('app', function($http,$timeout,$compile,$window,bootstrapModal,bui,access,growl,myPagination) {
	
	function app() {

		var self = this;

		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};

			scope.criteria = {};
			scope.filter = {};
			scope.documents = [];
			
			popFilter(scope);
			
		};
		
		function popFilter(scope) {
			
			$http({
				method: 'GET',
				url: 'document/filters'
			}).then(function mySuccess(response) {
				
				scope.criteria = angular.copy(response.data);
				
				scope.filter.origin = {"id":0,"office":"All","shortname":"All"};
				scope.filter.communication = {"id":0,"communication":"All","shortname":"All"};
				scope.filter.document_transaction_type = {"id":0,"transaction":"All","days":"All"};
				scope.filter.doc_type = {"id":0,"document_type":"All"};
					
			}, function myError(response) {
				
		
			});
			
		};
		
		self.filter = function(scope) {
			
			bui.show();
			
			myPagination.init(scope);

            scope.orderByAttribute = 'id';
            scope.sortReverse = false; // set the default sort order
			scope.pagination = {
				url: 'document/filter/',
				count: 0,
                currentPage: 1,
                entryLimit: 25,
                noOfPages: 5,
				filters: scope.filter
			};			

			myPagination.count(scope.pagination.url+'0/1',scope.filter).then((response) => {
                scope.pagination.count = response.data.count;
                pagesLinks = [];
				bui.hide();
                myPagination.getList(scope.pagination.url+scope.pagination.entryLimit+'/'+scope.pagination.currentPage, scope.filter).then((response) => {
					scope.documents = response.data;
					bui.hide();
                });
            });		
			
		};
		
		self.view = function(scope,d) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.view)) return;
			
			$window.location.href = 'document/action/'+d.id;
			
		};

	};

	return new app();

});