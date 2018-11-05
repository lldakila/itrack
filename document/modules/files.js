angular.module('files-module',['bootstrap-modal']).factory('files', function($timeout,bootstrapModal) {
	
	function files() {
		
		var self = this;
		
		self.filesThumbnails = function(scope,files) {
			
			var eids = {
				jpeg: "#dfimg",
				png: "#dfimg",					
				pdf: "#dfpdf"
			};			
			
			angular.forEach(files, function(file,i) {			

				var eid = eids[file.type]+i;

				scope.documentFiles.push({type: file.type, name: file.name});

				scope.documentFiles[i]['eid'] = eid;

			});
			
			$timeout(function() { previewThumbnails(scope,files); },500);			
			
		};
		
		function previewThumbnails(scope,files) {
			
			angular.forEach(scope.documentFiles, function(df,i) {
				
				var preview = document.querySelector(df.eid);				
				
				if (df.type == "pdf") {
					preview.data = files[i].file;					
				} else {
					preview.src = files[i].file;
				};
				
			});

		};
		
		self.previewThumbnail = function(scope,file,dialog) {
			
			var e = document.querySelector(file.eid);
			
			$timeout(function() {
			
				var t = document.querySelector((file.type=="pdf")?"#dfpdf-preview":"#dfimg-preview");

				scope.preview.file.type = file.type;

				if (file.type=="pdf") {
					t.data = e.data;
				} else {
					t.src = e.src;
				};
				
			}, 1000);
			
			bootstrapModal.box3(scope,'Preview File',dialog,function() {},"150");

		};		
		
	};
	
	return new files();
	
});