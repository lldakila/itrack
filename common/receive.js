angular.module('receive-document',['module-access','block-ui','bootstrap-growl','bootstrap-modal']).factory('receive', function($window,$timeout,$http,access,bui,growl,bootstrapModal) {
	
	function receive() {
		
		var self = this;
		
		self.document = function(scope,id) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.receive)) return;			
			
			scope.doc = {};
			scope.doc.file = false;
			
			var onOk = function() {
			
				bui.show();
			
				$http({
				  method: 'POST',
				  url: 'document/doc/transit/receive/'+id,
				  data: scope.doc,
				}).then(function mySuccess(response) {

					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

					bui.hide();

				}, function myError(response) {

					bui.hide();

				});
				
				return true;
				
			};
			
			bootstrapModal.box(scope,'Receive document','/dialogs/receive.html',onOk);			
			
		};
		
	};
	
	return new receive();
	
});