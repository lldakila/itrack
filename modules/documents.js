angular.module('app-module', ['bootstrap-modal','ui.bootstrap','window-open-post','notifications-module','ngRoute','block-ui','bootstrap-growl']).config(function($routeProvider) {
    $routeProvider.when('/:option/:id', {
        templateUrl: 'transact.html'
    });	
}).factory('app', function($http,$timeout,$compile,$window,bootstrapModal,printPost,$routeParams,$location,$q,bui,growl) {
	
	function app() {

		var self = this;
		
		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};
			
			scope.views.error = {};
			scope.views.error.pickup = {
				show: false,
				msg: ''
			};
			scope.views.error.chosen = {
				show: false,
				msg: ''
			};			
			
			scope.btns = {};
			scope.btns.transact = {
				controls: {
					edit: false,
					ok: true,
					cancel: false
				},
				labels: {
					edit: 'Transact',
					ok: 'Update',
					cancel: 'Close'		
				}
			};
			
			scope.activity = {};
			
			scope.document = {};	
			scope.documents = [];
			
			scope.views.currentPage = 1; // for pagination
			
			/* scope.$watch(function(scope) {
				
				return scope.search;
				
			},function(newValue, oldValue) {
				
				$timeout(function() { $('[data-toggle="tooltip"]').tooltip(); },500);
				
			});	 */		

			scope.$on('$routeChangeSuccess', function() {

				if ($routeParams.option != undefined) {
					
					if ($routeParams.id != undefined) {
						
						$timeout(function() {
							
							$http({
							  method: 'POST',
							  url: 'handlers/document.php',
							  data: {id: $routeParams.id}
							}).then(function mySuccess(response) {

								self.activity(scope,response.data);

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
			
			scope.btns.transact = {
				controls: {
					edit: false,
					ok: true,
					cancel: false
				},
				labels: {
					edit: 'Transact',
					ok: 'Update',
					cancel: 'Close'		
				}
			};			
			
			scope.activity = {};
			
			scope.views.title = 'For Transactions';
			scope.views.search = false;
			
			scope.currentPage = scope.views.currentPage;
			scope.pageSize = 10;
			scope.maxSize = 5;
			
			$http({
			  method: 'GET',
			  url: 'handlers/documents.php'
			}).then(function mySuccess(response) {
				
				scope.documents = angular.copy(response.data);
				scope.filterData = scope.documents;
				scope.currentPage = scope.views.currentPage;
				// $timeout(function() { $('[data-toggle="tooltip"]').tooltip(); },500);
				
			}, function myError(response) {
				
			});
			
			$('#content').load('lists/transact.html',function() {
				$timeout(function() { $compile($('#content')[0])(scope); }, 500);
			});			
			
		};		

		self.refresh = function(scope) {
			
			self.activity(scope,scope.document,false);
			
		};
		
		self.activity = function(scope,doc,form) {

			bui.show();
		
			scope.document = angular.copy(doc);
		
			scope.views.title = '';		
			scope.views.search = true;

			scope.activity.next = {};

			options(scope);
			offices(scope);

			scope.staffs = [];
			
			$http({
			  method: 'POST',
			  url: 'handlers/doc-activity.php',
			  data: {id: doc.document.id}
			}).then(function mySuccess(response) {
				
				scope.activity.document = response.data.document;			
			
				scope.activity.tracks = response.data.tracks;
				scope.activity.files = response.data.files;
				scope.activity.attachments = response.data.attachments;
				
				bui.hide();

			}, function myError(response) {
				
				//
				bui.hide();
				
			});

			if (form) {
				$('#content').load('forms/transact.html',function() {
					$timeout(function() { $compile($('#content')[0])(scope); }, 500);
				});
			};

		};

		self.transact = function(scope) {
			
			scope.btns.transact.controls.edit = true;
			scope.btns.transact.controls.ok = false;
			scope.btns.transact.labels.cancel = 'Cancel';
			
		};

		self.cancel = function(scope) {
			
			if (scope.btns.transact.controls.ok) {
				self.list(scope);
			} else {
				scope.btns.transact.controls.edit = false;
				scope.btns.transact.controls.ok = true;
				scope.btns.transact.labels.cancel = 'Close';				
				delete scope.activity.action;
				if (scope.formHolder.activity.action.$touched) scope.formHolder.activity.action.$touched = false;
				scope.views.error.pickup.show = false;
				scope.views.error.pickup.msg = '';	
				
				scope.views.error.chosen.show = false;
				scope.views.error.chosen.msg = '';
			};

		};

		self.save = function(scope) {

			scope.views.error.pickup.show = false;
			scope.views.error.pickup.msg = '';	
			
			scope.views.error.chosen.show = false;
			scope.views.error.chosen.msg = '';			
			
			if (scope.activity.action == 'Forward') {
			
				okForPickUp(scope).then(function success(response) {

					update(scope);
					
				}, function error(response) {

					scope.views.error.pickup.show = true;
					scope.views.error.pickup.msg = 'Document cannot be forwarded if it has no flag yet';

				});	

			} else {

				update(scope);

			};
			
			function update(scope) {

				if (validate(scope,'activity')) return;
	
				scope.activity.options = scope.options;

				if (scope.activity.action == 'Flag') {
				
					if (!hasChosenOption(scope)) {

						scope.views.error.chosen.show = true;
						scope.views.error.chosen.msg = 'Please choose one option';
						
						return;

					};
					
				};
				
				bui.show();
				
				$http({
				  method: 'POST',
				  url: 'handlers/doc-transaction.php',
				  data: scope.activity
				}).then(function mySuccess(response) {

					bui.hide();
					scope.btns.transact.controls.edit = false;
					scope.btns.transact.controls.ok = true;
					scope.btns.transact.labels.cancel = 'Close';					
					
					var action = scope.activity.action;
					notify(scope,action);
					
					self.activity(scope,scope.activity,false);
					
					$timeout(function() {
						delete scope.activity.action;
					},500);

				}, function myError(response) {

				});			
			
			};
			
		};
		
		function notify(scope,action) {			
			
			var text = '';

			switch (action) {
				
				case 'Flag':
					text = 'Document was '+(chosenOption(scope))['description'];
				break;
				
				case 'Forward':
					text = 'Document is ready for pick up';
				break;
				
				case 'File':
					text = 'Document was moved to archived';
				break;
				
			};
			
			growl.show('success',{from: 'top', amount: 55},text);			
			
		};
		
		function okForPickUp(scope) {
			
			return $q(function(resolve, reject) {

				if (scope.activity.tracks[0].ds == 'Received') reject(true);
				else resolve(false);
				
			});
			
		};
		
		function options(scope) {

			scope.options = [];
		
			$http({
				method: 'GET',
				url: 'handlers/options.php'
			}).then(function mySuccess(response) {
				
				scope.options = angular.copy(response.data);
					
			}, function myError(response) {
				
		
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
		
		self.optionChosen = function(scope,opt) {

			var index = scope.options.indexOf(opt);
			
			angular.forEach(scope.options,function(option,i) {
				
				if (typeof option.value === 'boolean') {

					if (i != index) {
						scope.options[i].value = false;
					};

				};

			});
			
		};
		
		function hasChosenOption(scope) {
			
			var chosen = false;
			
			angular.forEach(scope.options,function(option,i) {
				
				if (typeof option.value === 'boolean') {

					chosen = true;

				};

			});
			
			return chosen;
			
		};
		
		function chosenOption(scope) {
			
			var chosen = {};
			
			angular.forEach(scope.options,function(option,i) {
				
				if (typeof option.value === 'boolean') {

					chosen = option;

				};

			});
			
			return chosen;
			
		};		
		
		self.preview = function(file) {

			printPost.show('preview/index.php',file);
			
		};
		
		self.errorDisplayReset = function(scope) {
			
			scope.views.error.pickup.show = false;
			scope.views.error.pickup.msg = '';				
			scope.views.error.chosen.show = false;
			scope.views.error.chosen.msg = '';			
			
		};

	};

	return new app();

});