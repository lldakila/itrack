angular.module('app-module', ['form-validator','bootstrap-modal','ui.bootstrap','notifications-module']).factory('app', function($http,$timeout,$compile,$window,validate,bootstrapModal) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};

			barcodeAsyncSuggest(scope);
			
			scope.document = {};
			scope.doc = {};			

		};

		function barcodeAsyncSuggest(scope) {

			scope.barcodeAsyncSuggest = function(f) {
				
				return $http({
				  method: 'POST',
				  url: 'handlers/track-barcode-async-suggest.php',
				  data: {filter: f}
				}).then(function mySucces(response) {
					
					return response.data;
					
				},
				function myError(response) {

				});					
				
			};

			scope.barcodeAsyncSuggest('');

		};		

		self.barcodeSelected = function(scope,item) {
			
			scope.document = item;
			
		};	

		self.track = function(scope) {
			
			$('#track').html('');
			
			if (scope.document.id === undefined) {
				$('#track').html('<div class="col-lg-4 offset-lg-4"><div class="alert alert-danger">No document found.</div></div>');
				return;
			};
			
			var loading = '<div class="col-lg-12">Fetching document tracks please wait...</div>';
			
			$('#track').html(loading);
			
			scope.doc = angular.copy(scope.document);			
			
			$http({
			  method: 'POST',
			  url: 'handlers/track-document.php',
			  data: {id: scope.document.id}
			}).then(function mySuccess(response) {
				
				// delete scope.document.id;
				scope.doc = angular.copy(response.data.document);
				scope.tracks = response.data.tracks;				
				
				$('#track').load('html/tracks.html',function() {
					$timeout(function() { $compile($('#track')[0])(scope); }, 100);
				});
				
			}, function myError(response) {

			});			
			
		};

	};

	return new app();

});