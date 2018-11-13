angular.module('app-module', ['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-action','receive-document']).factory('app', function($http,$timeout,$compile,$window,bootstrapModal,bui,access,growl,receive) {
	
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
			
			scope.currentPage = 1;
			scope.pageSize = 10;
			scope.maxSize = 5;			
			
			$http({
				method: 'POST',
				url: 'document/filter',
				data: scope.filter,
			}).then(function mySuccess(response) {
				
				bui.hide();
				
				scope.documents = response.data;				
				scope.filterData = scope.documents;
				scope.currentPage = 1;
					
			}, function myError(response) {
				
				bui.hide();				
		
			});			
			
		};
		
		self.receive = function(scope,d) {

			receive.document(scope,d.id);
			
		};

	};

	return new app();

});