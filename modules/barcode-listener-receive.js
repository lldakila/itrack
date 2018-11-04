angular.module('barcode-listener-action',['bootstrap-modal','form-validator-dialog','bootstrap-growl','receive-document']).directive('listenBarcode', function($document,$rootScope,$http,$window,bootstrapModal,validateDialog,growl,$q,receive) {

	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
		
			$(document).scannerDetection({
				timeBeforeScanTest: 200, // wait for the next character for upto 200ms
				startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
				endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
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
			
				bootstrapModal.box(scope,'Enter document barcode to receive','/itrack/dialogs/manual-barcode.html',onOk,function() {});
				
			};
			
			function barcode(barcode, qty) {
				
				$http({
					method: 'GET',
					url: '/itrack/api/documents/barcode/'+barcode,
				}).then(function success(response) {

					// check if barcode is valid
					if (response.data.status) {
						receive.document(scope,response.data.id);
					} else {
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Invalid barcode. No document found.');					
					};
					
				}, function error(response) {				

				});
				
			};

		}
	};	

});