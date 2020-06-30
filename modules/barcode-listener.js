angular.module('barcode-listener',['bootstrap-modal','form-validator-dialog','bootstrap-growl']).directive('listenBarcode', function($document,$rootScope,$http,$window,bootstrapModal,validateDialog,growl,$q) {

	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
		
			$(document).scannerDetection({
				timeBeforeScanTest: 200, // wait for the next character for upto 200ms
				// startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
				// endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
				avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
				onComplete: barcode // main callback function	
			});
			
			$document.bind('keydown', function(e) {
				if ((e.ctrlKey) && (e.which === 13)) {
					manual_barcode(scope);
				};
				// $rootScope.$broadcast('keypress', e);
				// $rootScope.$broadcast('keypress:' + e.which, e);				
			});			
			
			function manual_barcode(scope) {
			
				delete scope.barcode;
			
				var onOk = function() {
					
					if (validateDialog.form(scope,'barcode')) return false;
					
					barcode(scope.barcode,0);
					
					return true;
					
				};
			
				bootstrapModal.box(scope,'Manual Barcode','dialogs/manual-barcode.html',onOk,function() {});
				
			};
			
			function barcode(barcode, qty) {
				
				$http({
					method: 'GET',
					url: 'api/documents/barcode/'+barcode,
				}).then(function success(response) {

					// check if barcode is valid
					if (response.data.status) {
						view_document(response.data.id);
					} else {
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Invalid barcode. No document found.');					
					};
					
				}, function error(response) {				
				
				});
				
			};

			function view_document(id) {
				
				assess_document(id).then(function success(response) {
					
					if (response.ok) {
					
						switch (response.action) {

							case 1:

								$window.location.href = 'document/for/initial/'+id;
							
							break;
							
							case 2:

							break;
							
							case 3:

							break;

						};
						
					} else {
						
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'You have no pre-defined action for this document.');
						
					};
					
				}, function error(response) {
					
					
					
				});
				
			};

			function assess_document(id) {

				return $q(function(resolve, reject) {
			
					$http({
						method: 'GET',
						url: 'document/track/assess/'+id,
					}).then(function success(response) {

						resolve(response.data);

					}, function error(response) {				

						reject(false);		

					});
				
				});			
			
				/* var deferred = $q.defer();				

				$http({
					method: 'GET',
					url: 'document/track/assess/'+id,
				}).then(function success(response) {

					deferred.resolve(response.data);

				}, function error(response) {				

					deferred.reject(false);				

				});

				return deferred.promise; */

			};

		}
	};	

});