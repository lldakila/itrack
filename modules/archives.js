angular.module('app-module', ['form-validator','bootstrap-modal','window-open-post','notifications-module','block-ui']).factory('app', function($http,$timeout,$compile,$window,validate,bootstrapModal,printPost,bui) {

	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			scope.views = {};

			scope.activity = {};
			
			scope.archive = {};
			scope.archives = [];	
			
			scope.views.currentPage = 1;			
			
			self.list(scope);

		};

		self.list = function(scope) {			
			
			if (scope.$id > 2) scope = scope.$parent;			
			
			scope.views.title = 'Archives';
			scope.views.search = false;			
			
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 5;			

			$http({
			  method: 'GET',
			  url: 'handlers/archives.php'
			}).then(function mySuccess(response) {

				scope.archives = angular.copy(response.data);
				scope.filterData = scope.archives;
				scope.currentPage = scope.views.currentPage;	

			}, function myError(response) {

				//

			});		

			$('#content').load('lists/archive.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});				

		};

		self.refresh = function(scope) {
			
			self.view(scope,scope.archive);
			
		};
		
		self.view = function(scope,archive) {			
			
			bui.show();
			
			scope.archive = angular.copy(archive);
		
			scope.views.title = '';
			scope.views.search = true;

			$http({
			  method: 'POST',
			  url: 'handlers/doc-activity.php',
			  data: {id: archive.id}
			}).then(function mySuccess(response) {

				scope.activity.document = response.data.document;			
				scope.activity.tracks = response.data.tracks;
				scope.activity.files = response.data.files;
				scope.activity.attachments = response.data.attachments;
				
				bui.hide();

			}, function myError(response) {

				bui.hide();
				
			});				

			$('#content').load('forms/archive.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});

		};
		
		self.preview = function(file) {

			printPost.show('preview/index.php',file);
			
		};			

	};

	return new app();

});