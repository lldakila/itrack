angular.module('file-document',['module-access','block-ui','bootstrap-growl','bootstrap-modal']).factory('file', function($window,$timeout,$http,access,bui,growl,bootstrapModal) {

	function file() {

		var self = this;

		self.document = function(scope,id) {

			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.file)) return;			

			var onOk = function() {

				bui.show();

				$http({
				  method: 'POST',
				  url: 'document/doc/transit/file/'+id,
				  data: scope.doc,
				}).then(function mySuccess(response) {

					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

					bui.hide();

				}, function myError(response) {

					bui.hide();

				});
			
			};
			
			bootstrapModal.box(scope,'File document','/dialogs/file.html',onOk);
			
		};
		
	};
	
	return new file();
	
});