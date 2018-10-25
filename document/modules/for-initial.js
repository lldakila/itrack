angular.module('app-module', ['form-validator','bootstrap-modal','jspdf-module','upload-files','block-ui','module-access','notifications-module','app-url','bootstrap-growl','files-module']).factory('app', function($http,$timeout,$window,validate,bootstrapModal,jspdf,uploadFiles,bui,access,url,growl,files) {
	
	function app() {

		var self = this;

		self.startup = function(scope) {
				
			
		};

		self.data = function(scope) {

			url.init(scope);

			jspdf.init();

			scope.formHolder = {};
			scope.views = {};

			var href = $window.location.href;

			var href_arr = href.split("/");

			var id = href_arr[href_arr.length-1];		

			scope.doc = {};
			scope.doc.id = id;
			scope.doc.initial = false;
			
			initDoc(scope,id);

			scope.preview = {};
			scope.preview.file = {};		

		};
		
		self.previewThumbnail = files.previewThumbnail;
		
		function initDoc(scope,id) {
			
			bui.show("Fetching document info please wait...");

			scope.documentFiles = [];
			
			$http({
			  method: 'GET',
			  url: scope.url.for+'document/for/initial/doc/'+id,
			}).then(function mySuccess(response) {

				files.filesThumbnails(scope,response.data.files);

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});			
			
		};
		
	};
	
	return new app();
});
