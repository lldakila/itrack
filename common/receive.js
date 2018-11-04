angular.module('receive-document',['module-access','block-ui','bootstrap-growl']).factory('receive', function($window,$timeout,$http,access,bui,growl) {
	
	function receive() {
		
		var self = this;
		
		self.document = function(scope,id) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.receive)) return;
			
			bui.show();
			
			$http({
			  method: 'GET',
			  url: 'document/doc/transit/receive/'+id,
			}).then(function mySuccess(response) {

				growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});
			
		};
		
	};
	
	return new receive();
	
});