angular.module('app-module', ['form-validator','bootstrap-modal','jspdf-module','upload-files','block-ui','module-access','notifications-module','app-url','bootstrap-growl','files-module','barcode-listener-action','form-validator-dialog','file-uploader']).factory('app', function($http,$timeout,$window,validate,bootstrapModal,jspdf,uploadFiles,bui,access,url,growl,files,validateDialog,fileUpload) {
	
	function app() {

		var self = this;

		self.startup = function(scope) {
			
			scope.files = {};
			scope.progress = {};
			scope.progress.show = false;
			scope.progress.status = 0;
			
			scope.disabled = {};
			scope.disabled.transit = {};
			scope.disabled.transit.pickup = false;
			scope.disabled.transit.release = false;
			
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
			scope.doc.barcode = $('#barcode').html();
			scope.doc.actions = [];
			scope.doc.tracks = [];
			
			scope.documentFiles = [];
			
			initDoc(scope,id);
			check_pickup_release(scope,id);

			scope.preview = {};
			scope.preview.file = {};

			offices(scope);
			staffs(scope);
			
			scope.staffs = {};
			
			scope.comment = {};
			scope.transit = {};
			scope.release = {};
			
			scope.revisions = [];

			scope.controls = { // for remove-file-edit to work
				btns: {
					save: false
				}
			};

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
				if (staff.done) staff.done = !staff.done;
				return;
			};
			
			/*
			** verify
			*/

			$http({
			  method: 'POST',
			  url: scope.url.view+'document/doc/actions/revisions/verify',
			  data: {id: scope.doc.id, action: action}
			}).then(function mySuccess(response) {
				
				if (response.data.status) {

					updateAction();
				
				} else {

					growl.show('alert alert-danger no-border mb-2',{from: 'top', amount: 60},response.data.notify);
					if (staff.done) staff.done = !staff.done;					
				
				};
				
			}, function myError(response) {
				
			});

			
			function updateAction() {
			
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
				check_pickup_release(scope,scope.doc.id);

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
				check_pickup_release(scope,scope.doc.id);			

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
				
				if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.add_revision)) {
					return;
				};				

				scope.revision = {};
				scope.revision.id = 0;
				
				scope.files = {};
				scope.progress = {};
				scope.progress.show = false;
				scope.progress.status = 0;				
				
				scope.files.revision = null;				
				
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
					
					/* if (!access.has(scope,scope.profile.group,scope.module.id,scope.module.privileges.edit_revision)) {
						return false;
					};					
					
					if (validateDialog.form(scope,'revision')) return false;					
					
					if (scope.revision.id>0) {
						
						self.revisions.update(scope,scope.revision);						
						
					} else {
						
						self.revisions.save(scope);						
						
					}; */					
					
					return true;
					
				};
				
				bootstrapModal.box4(scope,'Document revision for '+$('#barcode').html(),'/dialogs/revision.html',function (){},onOk,'100');
				
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
				  url: scope.url.view+'document/doc/revisions/update/'+scope.document_id,
				  data: revision
				}).then(function mySuccess(response) {
					
					growl.show('alert alert-success no-border mb-2',{from: 'top', amount: 60},'Revision successfully updated.');					
					self.revisions.list(scope);

				}, function myError(response) {
					
				});					
				
			},
			
			updateStatus: function(scope,revision) {
				
				$http({
				  method: 'PUT',
				  url: scope.url.view+'document/doc/revisions/update/status/'+scope.document_id,
				  data: revision
				}).then(function mySuccess(response) {

					self.revisions.list(scope);

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
				
			},
			
			upload: function(scope) {
				
			   var file = scope.files.revision;
			   
			   if (file == undefined) return;
			   
			   var rf = file['name'];
			   var en = rf.substring(rf.indexOf("."),rf.length);

				if ((en==".jpg") || (en==".png") || (en==".pdf")) {

					var uploadUrl = "/document/doc/revisions/upload/"+scope.document_id+"/"+rf;
					fileUpload.uploadFileToUrl(file, uploadUrl, scope);
					
				}
			
			},
			
			preview_uploaded_file: function(scope,r) {
				
				scope.previews = {};
				scope.previews.revision = {};
				scope.previews.revision.type = "";				
				
				bootstrapModal.box3(scope,'Preview File','/document/dialogs/revision-preview.html',function() {},"150");				
				
				var arr_fn = r['uploaded_file'].split(".");
				scope.previews.revision.type = arr_fn[1];				
				
				$timeout(function() {
					
					var t = document.querySelector((scope.previews.revision.type=="pdf")?"#uploaded-revision-pdf-preview":"#uploaded-revision-img-preview");													

					if (scope.previews.revision.type=="pdf") {
						t.data = "/revisions/"+r.document_id+"/"+r['uploaded_file'];
					} else {
						t.src = "/revisions/"+r.document_id+"/"+r['uploaded_file'];
					};

				},1000);
				
			}
			
		};
		
		// upload file
		self.addFile = function(scope) {
			
			$('#upload-files')[0].click();
			
		};
		
		self.close = function(scope) {
			
			$window.location.href = "/update-tracks.html";
			
		};
		
		function check_pickup_release(scope,id) {
			
			$http({
				method: 'GET',
				url: '/document/check/pickup_release/'+id,
			}).then(function succes(response) {
				
				scope.disabled.transit = response.data;
				
			}, function error(response) {
				
			});
			
		};
		
	};
	
	return new app();
	
}).directive('actionUploadFiles',function($timeout,$q,$http,files,bui) {

	return {
		restrict: 'A',
		link: function(scope, element, attrs) {

			scope.documentFiles = [];
		
			element.bind('click', function() {


			});

			element.bind('change', function() {

				var _files = $('#upload-files')[0].files;
				var types = {
					"pdf": "data",
					"jpeg": "src",
					"png": "src",
				};
				
				scope.doc.files = [];

				angular.forEach(_files, function(file,n) {					
					
					var type = file.type.split("/");
					
					if ( (type[1] != "jpeg") && (type[1] != "png") && (type[1] != "pdf") ) return; 
					
					scope.$apply(function() {
						scope.documentFiles.push({type: type[1]});
					});
				
					var i = scope.documentFiles.length-1;
					
					var eid = "#dfpdf"+i;
					if (type[1] != 'pdf') eid = "#dfimg"+i;
					var preview = document.querySelector(eid);
					var reader  = new FileReader();

					reader.addEventListener("load", function () {
						if (type[1] == "pdf") preview.data = reader.result;
						else preview.src = reader.result;
						scope.documentFiles[i].eid = eid;
						scope.doc.files.push({file: reader.result, type: type[1], name: null});						
					}, false);
					
					reader.addEventListener("loadend", function () {
						upload(scope);
					}, false);					
					
					if (file) {
						reader.readAsDataURL(file);
					};

				});

				function upload(scope) {
				
					$http({
						method: 'PUT',
						url: scope.url.view+'document/doc/revisions/upload/files',
						data: scope.doc
					}).then(function succes(response) {
						
						initDoc(scope,scope.document_id);						
						
					}, function error(response) {

					});

				};
				
				function initDoc(scope,id) {
					
					bui.show();
					scope.documentFiles = [];
					
					$http({
					  method: 'GET',
					  url: scope.url.view+'document/doc/actions/'+id,
					}).then(function mySuccess(response) {

						files.filesThumbnails(scope,response.data.files);

						bui.hide();

					}, function myError(response) {

						bui.hide();

					});					
					
				};

			});
			
		}
	};
		
}).directive('actionRemoveFile',function($timeout,$http) {
	
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
		
			element.bind('click', function() {
			
				var index = attrs.actionRemoveFile;
				
				file = scope.documentFiles[index];
				
				$http({
					method: 'DELETE',
					url: scope.url.view+'document/doc/revisions/delete/files/'+file.id+'/'+file.name,
				}).then(function succes(response) {
					
				}, function error(response) {

				});

				delete scope.documentFiles.splice(index,1);
				scope.$apply();
				
			});

		}
	};	
	
});
