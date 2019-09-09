angular.module('app-module', ['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-receive','receive-document']).factory('app', function($http,$timeout,$compile,$window,bootstrapModal,bui,access,growl,receive,$q) {
	
	function app() {

		var self = this;

		self.data = function(scope) {

			scope.formHolder = {};
			
			scope.views = {};

			scope.criteria = {};
			scope.filter = {};
			scope.documents = [];
			
			popFilter(scope);
			
		};
		
		function popFilter(scope) {
			
			$http({
				method: 'GET',
				url: 'document/filters'
			}).then(function mySuccess(response) {
				
				scope.criteria = angular.copy(response.data);
				
				scope.filter.origin = {"id":0,"office":"All","shortname":"All"};
				scope.filter.communication = {"id":0,"communication":"All","shortname":"All"};
				scope.filter.document_transaction_type = {"id":0,"transaction":"All","days":"All"};
				scope.filter.doc_type = {"id":0,"document_type":"All"};
					
			}, function myError(response) {
				
		
			});
			
		};
		
		self.filter = function(scope) {
			
			bui.show();
			
			scope.currentPage = 1;
			scope.pageSize = 10;
			scope.maxSize = 5;			
			
			$http({
				method: 'POST',
				url: 'document/filter',
				data: scope.filter,
			}).then(function mySuccess(response) {
				
				bui.hide();
				
				scope.documents = response.data;
				scope.filterData = scope.documents;
				scope.currentPage = 1;
				
				$(function() {
				  $('[data-toggle="tooltip"]').tooltip();
				});
				
			}, function myError(response) {
				
				bui.hide();				
		
			});		
			
		};
		
		self.receive = function(scope,d) {

			receive.document(scope,d.id);
			
		};
		
		self.open = function(scope,d) {	
			
			isReceive(scope,d).then(function success(response) {
				
				if (!response) {
					
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Document is not yet received in your office');
					
				} else {
					
					open(scope);
					
				}
				
			}, function error(response) {
				
				
			});
			
			function open(scope) {
				
				scope.doc = {};
				scope.doc.id = d.id;
				scope.doc.actions = [];
				
				scope.views.alert = {};
				scope.views.alert.actions = {};
				scope.views.alert.actions.show = false;
				scope.views.alert.actions.message = "";
				
				$http({
					method: 'GET',
					url: 'document/view/actions/'+d.id
				}).then(function mySuccess(response) {
					
					scope.doc.actions = angular.copy(response.data);
					checkboxActionParam(scope);

				}, function myError(response) {
			
			
				});
				
				var onOk = function() {
					
					bui.show();
					
					scope.views.alert.actions.show = false;
					scope.views.alert.actions.message = "";			
					
					var actions = 'false';
					var options = 'true';

					var doc_actions = ['for_initial','for_signature','for_routing'];

					doc_actions.forEach(function(action,i) {
						
						actions+=(scope.doc.actions[action].value)?'||true':'||false';
						
						if (scope.doc.actions[action].value) {
							
							options+='&&(';
							
							angular.forEach(scope.doc.actions[action].params,function(param,ii) {
								
								if (param.type=='checkbox') {
								
									angular.forEach(param.options, function(option,iii) {
										
										if (iii==0) options+=(option.value)?'true':'false';
										else options+=(option.value)?'||true':'||false';						
										
									});
								
								};
								
								if (param.type=='select') {
									
									options+=(param.value.id>0)?'true':'false';						
									
								};
								
							});
							
							options+=')';
							
						};
						
					});
					
					if (!eval(actions)) {
						
						scope.views.alert.actions.show = true;
						scope.views.alert.actions.message = "Pleas select an action";
						bui.hide();
						return false;
						
					} else {
						
						if (!eval(options)) {

							scope.views.alert.actions.show = true;
							scope.views.alert.actions.message = "Choice is required in each actions selected";
							bui.hide();
							return false;
							
						};
						
					};

					$http({
						method: 'POST',
						url: 'document/doc/office/action',
						data: scope.doc
					}).then(function mySuccess(response) {					
				
						growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Action(s) for document updated.');
						bui.hide();

					}, function myError(response) {								
					
						bui.hide();
					});
					
					return true;
					
				};
				
				bootstrapModal.box(scope,'Document Actions','/dialogs/actions.html',onOk);
				
			};
			
		};
		
		function isReceive(scope,doc) {
			
		  return $q(function(resolve, reject) {
			  
			$http({
				method: 'GET',
				url: 'document/doc/transit/is_receive/'+doc.id,
			}).then(function mySuccess(response) {							
		
				if (response.data>0) {
					
					resolve(true);
					
				} else {

					resolve(false);
					
				}
	
			}, function myError(response) {					
			
				reject(null);

			});		
			
		  });			
			
		};
		
		self.headerActionParam = function(scope,action) {		
		
			scope.doc.actions[action].value = !scope.doc.actions[action].value;
			
		};		

		self.checkboxActionParam = function(scope,action) {
				
			if (scope.doc.actions[action].value) $('#'+action).addClass('in');
			else $('#'+action).removeClass('in');
			
		};
		
		function checkboxActionParam(scope) {
			
			var actions = ['for_initial','for_signature','for_routing'];
			
			actions.forEach(function(action,i) {

				if (scope.doc.actions[action].value) $('#'+action).addClass('in');
				else $('#'+action).removeClass('in');		

			});
			
		};		

	};

	return new app();

});