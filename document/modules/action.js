angular.module('app-module', ['form-validator','bootstrap-modal','jspdf-module','upload-files','block-ui','module-access','notifications-module','app-url','bootstrap-growl','files-module','barcode-listener-action','form-validator-dialog']).factory('app', function($http,$timeout,$window,validate,bootstrapModal,jspdf,uploadFiles,bui,access,url,growl,files,validateDialog) {
	
	function app() {

		var self = this;

		self.startup = function(scope) {
			
			self.revisions.list(scope);
			
		};

		self.data = function(scope) {

			url.init(scope);

			jspdf.init();

			scope.formHolder = {};
			scope.views = {};

			var href = $window.location.href;

			var href_arr = href.split("/");

			var id = href_arr[href_arr.length-1];
			
			scope.document_id = id;

			scope.doc = {};
			scope.doc.id = id;
			scope.doc.actions = [];
			scope.doc.tracks = [];
			
			initDoc(scope,id);

			scope.preview = {};
			scope.preview.file = {};

			offices(scope);
			staffs(scope);
			
			scope.staffs = {};
			
			scope.comment = {};
			scope.transit = {};
			scope.release = {};
			
			scope.revisions = [];			

		};
		
		function offices(scope) {
			
			$http({
				method: 'GET',
				url: scope.url.view+'document/offices'
			}).then(function mySuccess(response) {
				
				scope.offices = response.data;
					
			}, function myError(response) {
				
		
			});			
			
		};
		
		function staffs(scope) {
			
			$http({
				method: 'GET',
				url: scope.url.view+'document/office/staffs'
			}).then(function mySuccess(response) {
				
				scope.office_staffs = response.data;
					
			}, function myError(response) {

			
			});	
			
		};
		
		self.previewThumbnail = files.previewThumbnail;
		
		function initDoc(scope,id) {
			
			bui.show("Fetching document info please wait...");

			scope.documentFiles = [];
			
			$http({
			  method: 'GET',
			  url: scope.url.view+'document/doc/actions/'+id,
			}).then(function mySuccess(response) {
			
				scope.doc.actions = response.data.actions;
				scope.doc.tracks = response.data.tracks;
				
				files.filesThumbnails(scope,response.data.files);

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});			
			
		};
		
		self.action = function(scope,action,staff) {
			
			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.update)) {
				staff.done = !staff.done;
				return;
			};
			
			bui.show();
			
			$http({
			  method: 'POST',
			  url: scope.url.view+'document/doc/actions/update',
			  data: {id: scope.doc.id, action: action, staff: staff}
			}).then(function mySuccess(response) {

				var action_i = scope.doc.actions.indexOf(action);
				var staff_i = scope.doc.actions[action_i].staffs.indexOf(staff);			
				
				if (response.data.status) {				
					
					if (scope.doc.actions[action_i].staffs[staff_i].done) {
						scope.doc.actions[action_i].staffs[staff_i].action_track_id = response.data.action_track_id;
					};
					
					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');
					
				} else {
					
					scope.doc.actions[action_i].staffs[staff_i].done = !scope.doc.actions[action_i].staffs[staff_i].done;
					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Sorry, you are not allowed to update document tracks.');
					
				};

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});	
			
		};
		
		self.transit = function(scope) {

			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.transit)) {
				return;
			};
		
			if (validate.form(scope,'transit')) {
				
				growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Please select office and staff.');				
				return;
				
			};
			
			bui.show();
			
			$http({
			  method: 'POST',
			  url: scope.url.view+'document/doc/transit/pickup',
			  data: {document: scope.doc, transit: scope.transit},
			}).then(function mySuccess(response) {

				growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});				
			
		};
		
		self.release = function(scope) {

			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.release)) {
				return;
			};
		
			if (validate.form(scope,'release')) {
				
				growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},'Please select office and staff.');				
				return;
				
			};
			
			bui.show();
			
			$http({
			  method: 'POST',
			  url: scope.url.view+'document/doc/transit/release',
			  data: {document: scope.doc, release: scope.release},
			}).then(function mySuccess(response) {

				growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

				bui.hide();

			}, function myError(response) {

				bui.hide();

			});				
			
		};
		
		self.comment = function(scope) {

			if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.comment)) {
				return;
			};		
		
			if (scope.comment.staff != undefined) delete scope.comment.staff;
			if (scope.comment.text != undefined) delete scope.comment.text;
		
			var onOk = function() {

				if (validateDialog.form(scope,'comment')) return false;

				$http({
				  method: 'POST',
				  url: scope.url.view+'document/doc/actions/comment',
				  data: {document: scope.doc, comment: scope.comment},
				}).then(function mySuccess(response) {

					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Document track updated.');				

					bui.hide();

				}, function myError(response) {

					bui.hide();

				});					
				
				return true;

			};

			bootstrapModal.box(scope,'Document comment','/dialogs/comment.html',onOk,function() {});			

		};
		
		self.revisions = {
			
			list: function(scope) {

				if (scope.$id>2) scope = scope.$parent;
				
				$http({
				  method: 'GET',
				  url: scope.url.view+'document/doc/revisions/'+scope.document_id,
				}).then(function mySuccess(response) {
					
					scope.revisions = angular.copy(response.data);
					
				}, function myError(response) {
					
				});					
				
			},
			
			revision: function(scope,revision) {
				
				scope.revision = {};
				scope.revision.id = 0;
				
				if (revision!=null) {
					
					$http({
					  method: 'GET',
					  url: scope.url.view+'document/doc/revisions/edit/'+scope.document_id,
					}).then(function mySuccess(response) {

						scope.revision = angular.copy(response.data);

					}, function myError(response) {
						
					});						
					
				};
				
				var onOk = function() {
					
					if (validateDialog.form(scope,'revision')) return false;					
					
					self.revisions.save(scope);
					
				};
				
				bootstrapModal.box(scope,'Document revision','/dialogs/revision.html',onOk,function() {});
				
			},
			
			save: function(scope) {
			
				$http({
				  method: 'POST',
				  url: scope.url.view+'document/doc/revisions/add/'+scope.document_id,
				  data: scope.revision
				}).then(function mySuccess(response) {
					
					if (scope.revision.id>0) growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Revision successfully updated.');
					else  growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Revision successfully added.');
						
					self.revisions.list(scope);
					
				}, function myError(response) {
					
				});						
				
				return true;
				
			},
			
			update: function(scope,revision) {
				
				$http({
				  method: 'PUT',
				  url: scope.url.view+'document/doc/revisions/update',
				  data: revision
				}).then(function mySuccess(response) {
					

				}, function myError(response) {
					
				});					
				
			},
			
			delete: function(scope,revision) {
				
				var onOk = function() {
					
					$http({
						method: 'DELETE',
						url: scope.url.view+'document/doc/revisions/delete/'+revision.id,
					}).then(function mySuccess(response) {
							
							growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 55},'Document revision successfully deleted.');
							self.revisions.list(scope);
							
					}, function myError(response) {
				

				
					});					
					
				};
				
				var onCancel = function() {};
				
				bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to delete this document type?',onOk,onCancel);
				
			}
			
		};
		
	};
	
	return new app();
});
