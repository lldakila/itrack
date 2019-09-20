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

					if (response.data.status==1) {

						growl.show('alert alert-info no-border mb-2',{from: 'top', amount: 60},'Document has already been received in your office');
					
					} else if (response.data.status==2) {
						
						growl.show('alert alert-info no-border mb-2',{from: 'top', amount: 60},'Document was picked up. You can file the document or define actions for it.');						
						
					} else if (response.data.status==3) {

						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},response.data.message);

					} else {

						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				
					
					}

					bui.hide();

				}, function myError(response) {

					bui.hide();

				});
				
				return true;
				
			};		
			
			$http({
			  method: 'GET',
			  url: 'document/doc/office/'+id,
			}).then(function mySuccess(response) {

				if (response.data==1) bootstrapModal.box(scope,'Receive document','/dialogs/receive.html',onOk);
				else bootstrapModal.box(scope,'Receive document','/dialogs/receive-only.html',onOk);

			}, function myError(response) {
				
			});
			
		};
		
	};
	
	return new receive();
	
});