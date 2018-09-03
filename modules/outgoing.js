angular.module('app-module', ['bootstrap-modal','ui.bootstrap','window-open-post','notifications-module','ngRoute']).config(function($routeProvider) {
    $routeProvider.when('/:option/:id', {
        templateUrl: 'outgoing.html'
    });	
}).factory('app', function($http,$timeout,$compile,$window,$routeParams,$location,bootstrapModal,printPost) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.activity = {};
			
			scope.outgoing = {};
			scope.outgoings = [];
			
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
							  url: 'handlers/outgoing.php',
							  data: {id: $routeParams.id}
							}).then(function mySuccess(response) {

								self.tracks(scope,response.data);

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
			
			scope.views.title = 'Outgoings';
			scope.views.search = false;			
			
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 5;
			
			$http({
			  method: 'GET',
			  url: 'handlers/outgoings.php'
			}).then(function mySuccess(response) {
				
				scope.outgoings = angular.copy(response.data);
				scope.filterData = scope.outgoings;
				scope.currentPage = scope.views.currentPage;
				
			}, function myError(response) {
				
			});

			$('#content').load('lists/outgoing.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});				
			
		};
		
		self.refresh = function(scope) {
			
			self.tracks(scope,scope.outgoing);
			
		};
		
		self.tracks = function(scope,outgoing) {

			scope.outgoing = angular.copy(outgoing);		
		
			scope.views.title = '';	
			scope.views.search = true;		

			scope.activity.next = {};
			offices(scope);

			scope.staffs = [];						
			
			scope.activity.show_action = outgoing.document.show_action;
			
			$http({
			  method: 'POST',
			  url: 'handlers/doc-activity.php',
			  data: {id: outgoing.document.id}
			}).then(function mySuccess(response) {
				
				scope.activity.document = response.data.document;
				scope.activity.tracks = response.data.tracks;
				scope.activity.files = response.data.files;
				scope.activity.attachments = response.data.attachments;

			}, function myError(response) {
				
				//
				
			});

			$('#content').load('forms/outgoing.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});			

		};
		
		self.save = function(scope) {
			
			if (validate(scope,'activity')) return false;				
			
			scope.activity.options = scope.options;
			
			$http({
			  method: 'POST',
			  url: 'handlers/doc-transaction.php',
			  data: scope.activity
			}).then(function mySuccess(response) {

				self.list(scope);

			}, function myError(response) {
				
				//
				
			});			
			
		};
		
		function offices(scope) {

			scope.offices = [];
		
			$http({
				method: 'GET',
				url: 'handlers/offices.php'
			}).then(function mySuccess(response) {
				
				scope.offices = angular.copy(response.data);
					
			}, function myError(response) {
				
		
			});			
		
		};
		
		self.preview = function(file) {

			printPost.show('preview/index.php',file);
			
		};		

	};
	
	return new app();
	
});