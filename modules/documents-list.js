angular.module('app-module', ['form-validator','ui.bootstrap','bootstrap-modal','window-open-post','notifications-module','block-ui']).factory('app', function($http,$timeout,$window,$compile,validate,bootstrapModal,printPost,bootstrapModal,bui) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};

			scope.activity = {};

			scope.document = {};			
			scope.documents = [];	
			
			scope.views.currentPage = 1;
			
			scope.$watch(function(scope) {
				
				return scope.search;
				
			},function(newValue, oldValue) {
				
				$timeout(function() { $('[data-toggle="tooltip"]').tooltip(); },500);
				
			});

		};		

		self.list = function(scope) {
			
			if (scope.$id > 2) scope = scope.$parent;
			
			scope.views.title = 'List of documents';
			scope.views.search = false;			
			
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 3;
			
			$http({
			  method: 'GET',
			  url: 'handlers/documents-list.php'
			}).then(function mySuccess(response) {

				scope.documents = angular.copy(response.data);	

				scope.filterData = scope.documents;
				scope.currentPage = scope.views.currentPage;				

			}, function myError(response) {

				//

			});

			$('#content').load('lists/document.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});			

		};
		
		self.refresh = function(scope) {
			
			self.view(scope,scope.document);
			
		};		
		
		self.view = function(scope,document) {

			bui.show();

			scope.document = angular.copy(document);			

			scope.views.title = '';
			scope.views.search = true;

			$http({
			  method: 'POST',
			  url: 'handlers/doc-activity.php',
			  data: {id: document.document.id}
			}).then(function mySuccess(response) {

				scope.activity.document = response.data.document;			
			
				scope.activity.tracks = response.data.tracks;
				scope.activity.files = response.data.files;
				scope.activity.attachments = response.data.attachments;

				bui.hide();
				
			}, function myError(response) {
				
				bui.hide();
				
			});	

			$('#content').load('forms/document.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});

		};

		self.delete = function(scope,doc) {
			
			scope.views.currentPage = scope.currentPage;

			var onOk = function() {
				
				$http({
					method: 'POST',
					url: 'handlers/delete-documents.php',
					data: {id: doc.id}
				}).then(function mySuccess(response) {

					self.list(scope);

				}, function myError(response) {

				});

			};

			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to delete this document?',onOk,function() {});

		};
		
		self.preview = function(file) {

			printPost.show('preview/index.php',file);
			
		};
		
	};

	return new app();

});