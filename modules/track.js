angular.module('app-module', ['form-validator','ui.bootstrap','ngSanitize','bootstrap-growl','track-document']).directive('trackDocument', function($rootScope,$http,$timeout,growl,app) {

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
			
			function barcode(barcode, qty) {
				
				$http({
					method: 'GET',
					url: '/api/documents/barcode/'+barcode,
				}).then(function success(response) {

					// check if barcode is valid
					if (response.data.status) {
						// $timeout(function() {
							app.track_document(scope,response.data.id);
						// }, 500);
					} else {
						growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Invalid barcode. No document found.');					
					};
					
				}, function error(response) {				

				});
				
			};

		}
	};	

}).factory('app', function($compile,$http,$timeout,validate,track) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.doc = {};
			scope.document = {};

		};

		self.track = function(scope) {
			
			if (validate.form(scope,'track')) return;
			
			$http({
				method: 'GET',
				url: '/api/documents/barcode/'+scope.doc.barcode,
			}).then(function success(response) {

				// check if barcode is valid
				if (response.data.status) {
					// $timeout(function() {
						self.track_document(scope,response.data.id);
					// }, 500);
				} else {
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Invalid barcode. No document found.');					
				};
				
			}, function error(response) {				

			});			

		};
		
		self.track_document = function(scope,id) {
			
			$('#tracks').html('Tracking document please wait...');
			
			$('#tracks').load('dialogs/track-document.html', function() {
			
				$compile($('#tracks')[0])(scope);
				
				track.get_document_tracks(scope,id).then(function success(data) {

					scope.document = angular.copy(data);
				
				}, function error(data) {

				});				

			});
			
		};
		
	};

	return new app();

});