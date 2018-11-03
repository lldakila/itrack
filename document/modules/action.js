angular.module('app-module', ['form-validator','bootstrap-modal','jspdf-module','upload-files','block-ui','module-access','notifications-module','app-url','bootstrap-growl','files-module','barcode-listener-action']).factory('app', function($http,$timeout,$window,validate,bootstrapModal,jspdf,uploadFiles,bui,access,url,growl,files) {
	
	function app() {

		var self = this;

		self.startup = function(scope) {
				
			
		};

		self.data = function(scope) {

			url.init(scope);

			jspdf.init();

			scope.viewmHolder = {};
			scope.views = {};

			var href = $window.location.href;

			var href_arr = href.split("/");

			var id = href_arr[href_arr.length-1];		

			scope.doc = {};
			scope.doc.id = id;
			scope.doc.actions = [];
			scope.doc.tracks = [];
			
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
			  url: scope.url.view+'document/doc/actions/'+id,
			}).then(function mySuccess(response) {
			
				scope.doc.actions = response.data.actions;
				scope.doc.tracks = response.data.tracks;
				
				files.filesThumbnails(scope,response.data.files);

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});			
			
		};
		
		self.action = function(scope,action,staff) {
			
			bui.show();
			
			$http({
			  method: 'POST',
			  url: scope.url.view+'document/doc/actions/update',
			  data: {id: scope.doc.id, action: action, staff: staff}
			}).then(function mySuccess(response) {

				var action_i = scope.doc.actions.indexOf(action);
				var staff_i = scope.doc.actions[action_i].staffs.indexOf(staff);
				
				if (scope.doc.actions[action_i].staffs[staff_i].done) {
					scope.doc.actions[action_i].staffs[staff_i].action_track_id = response.data;
				};
				
				growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});	
			
		};
		
	};
	
	return new app();
});
