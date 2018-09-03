angular.module('app-module', ['bootstrap-modal','ui.bootstrap','window-open-post','notifications-module','ngRoute']).config(function($routeProvider) {
    $routeProvider.when('/:option/:id', {
        templateUrl: 'incoming.html'
    });	
}).factory('app', function($http,$timeout,$compile,$window,bootstrapModal,printPost,$routeParams,$location) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};

			scope.views = {};

			scope.views.error = {};
			scope.views.error.receive = {
				show: false,
				msg: ''
			};
			scope.views.error.barcode = {
				show: false,
				msg: ''
			};			

			scope.receive = {};

			scope.activity = {};
			
			scope.incoming = {};
			scope.incomings = [];

			scope.views.currentPage = 1;

			/* scope.$watch(function(scope) {
				
				return scope.search;
				
			},function(newValue, oldValue) {
				
				$timeout(function() { $('[data-toggle="tooltip"]').tooltip(); },500);
				
			}); */
			
			scope.$on('$routeChangeSuccess', function() {

				if ($routeParams.option != undefined) {
					
					if ($routeParams.id != undefined) {
						
						$timeout(function() {
							
							$http({
							  method: 'POST',
							  url: 'handlers/incoming.php',
							  data: {id: $routeParams.id}
							}).then(function mySuccess(response) {

								self.receive(scope,response.data);

							}, function myError(response) {
								
							});								
							
						}, 1000);

					};
					
				};			

			});
			
			self.list(scope);
		
		};

		function validate(scope,form) {

			var controls = scope.formHolder[form].$$controls;

			angular.forEach(controls,function(elem,i) {

				if (elem.$$attr.$attr.required) {

					elem.$touched = elem.$invalid;

				};

			});

			return scope.formHolder[form].$invalid;

		};

		self.list = function(scope) {
						
			if (scope.$id > 2) scope = scope.$parent;
			
			scope.views.title = 'Incomings';
			scope.views.search = false;
			
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 5;
			
			$http({
			  method: 'GET',
			  url: 'handlers/incomings.php'
			}).then(function mySuccess(response) {
				
				scope.incomings = angular.copy(response.data);
				scope.filterData = scope.incomings;
				scope.currentPage = scope.views.currentPage;
				
			}, function myError(response) {
				
			});

			$('#content').load('lists/incoming.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});			
			
		};

		function barcodeAsyncSuggest(scope,doc) {

			scope.barcodeAsyncSuggest = function(f) {
				
				return $http({
				  method: 'POST',
				  url: 'handlers/barcode-async-suggest.php',
				  data: {id: doc.id, filter: f}
				}).then(function mySucces(response) {
					
					return response.data;
					
				},
				function myError(response) {

				});					
				
			};

			scope.barcodeAsyncSuggest('');

		};

		self.refresh = function(scope) {
			
			self.receive(scope,scope.incoming);
			
		};		
		
		self.receive = function(scope,doc) {

			scope.incoming = angular.copy(doc);		
		
			scope.views.title = '';
			scope.views.search = true;
			
			// barcodeAsyncSuggest(scope,doc);		

			scope.activity = angular.copy(doc);
			// scope.receive.receive_barcode = {id:0, barcode:''};
			scope.receive.receive_barcode = '';

			scope.activity.next = {};	

			scope.activity.next.option = [
				{description: 'Receive and transact', choice: 'transaction', value: false},
				{description: 'Receive and file', choice: 'filed', value: false}
			];

			$http({
			  method: 'POST',
			  url: 'handlers/doc-activity.php',
			  data: {id: doc.document.id}
			}).then(function mySuccess(response) {

				scope.activity.document = response.data.document;
				scope.activity.tracks = response.data.tracks;
				scope.activity.files = response.data.files;
				scope.activity.attachments = response.data.attachments;

			}, function myError(response) {
				
				//
				
			});			
			
			$('#content').load('forms/incoming.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});				

		};
		
		self.save = function(scope) {
			
			scope.views.error.barcode.show = false;
			scope.views.error.barcode.msg = '';			
			scope.views.error.receive.show = false;
			scope.views.error.receive.msg = '';
			
			if (validate(scope,'activity')) return false;
			
			if (scope.receive.receive_barcode != scope.activity.document.barcode) {
				scope.views.error.barcode.show = true;
				scope.views.error.barcode.msg = 'Barcode is incorrect';
				return;				
			};
			
			if (scope.activity.next.opt == undefined) {
				scope.views.error.receive.show = true;
				scope.views.error.receive.msg = 'Please select if document is for transaction or filing';
				return;
			};
			
			$http({
			  method: 'POST',
			  url: 'handlers/doc-receive.php',
			  data: scope.activity
			}).then(function mySuccess(response) {

				self.list(scope);

			}, function myError(response) {

				//

			});			
			
		};
		
		self.barcodeSelected = function(scope,item) {
			
			scope.receive.barcode = item;
			
		};		
		
		self.optionChange = function(scope,opt) {
			
			var index = scope.activity.next.option.indexOf(opt);
			
			angular.forEach(scope.activity.next.option, function(item,i) {
				
				if (i != index) scope.activity.next.option[i].value = false;
				
			});
			
			if (opt.value) {
				scope.activity.next.opt = opt.choice;
				scope.views.noOption = false;
			} else {
				delete scope.activity.next.opt;
				scope.views.noOption = true;				
			};

		};
		
		self.preview = function(file) {

			printPost.show('preview/index.php',file);
			
		};		
		
	};
	
	return new app();
	
});