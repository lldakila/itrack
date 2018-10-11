angular.module('barcode-listener',[]).directive('listenBarcode', function($document, $rootScope) {

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
					console.log('Enter barcode');
				};
				// $rootScope.$broadcast('keypress', e);
				// $rootScope.$broadcast('keypress:' + e.which, e);				
			});			
			
			function barcode(barcode, qty) {
				
				console.log(barcode);
				
			};
			
		}
	};	

});