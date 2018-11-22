angular.module('track-document',['module-access','block-ui','bootstrap-growl','bootstrap-modal']).factory('track', function($window,$timeout,$http,access,bui,growl,bootstrapModal) {
	
	function track() {
		
		var self = this;
		
		self.document = function(scope,id) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.track)) return;
			
			scope.document.id = id;
			
			var load = function() {
				
				bui.show();				
				
				$http({
				  method: 'GET',
				  url: 'document/doc/track/'+id,
				}).then(function mySuccess(response) {

					scope.document = response.data;
					
					bui.hide();

				}, function myError(response) {

					bui.hide();

				});				
				
			};
			
			bootstrapModal.box4(scope,'Document Tracks','dialogs/track-document.html',load,function() {},"150");
			
		};
		
		self.reload = function(scope) {
			
			bui.show();				
			
			$http({
			  method: 'GET',
			  url: 'document/doc/track/'+scope.document.id,
			}).then(function mySuccess(response) {

				scope.document = response.data;
				
				bui.hide();

			}, function myError(response) {

				bui.hide();

			});				
			
		};
		
	};
	
	return new track();
	
});