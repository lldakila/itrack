angular.module('upload-files', []).directive('addFiles',function($timeout) {

	return {
		restrict: 'A',
		link: function(scope, element, attrs) {

			scope.documentFiles = [];		
		
			element.bind('click', function() {


			});

			element.bind('change', function() {

				var files = $('#upload-files')[0].files;
				var types = {
					"pdf": "data",
					"jpeg": "src",
					"png": "src",
				};

				angular.forEach(files, function(file,n) {					
					
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
					}, false);

					if (file) {
						reader.readAsDataURL(file);
					};

				});

			});
			
		}
	};
		
}).directive('addAttachments',function($http) {
	
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {

			scope.attachmentFiles = [];		
		
			element.bind('click', function() {


			});

			element.bind('change', function() {

				var files = $('#upload-attachments')[0].files;
				var types = {
					"pdf": "data",
					"jpeg": "src",
					"png": "src",
				};				

				angular.forEach(files, function(file,n) {

					var type = file.type.split("/");		

					if ( (type[1] != "jpeg") && (type[1] != "png") && (type[1] != "pdf") ) return; 
				
					scope.$apply(function() {
						scope.attachmentFiles.push({type: type[1]});
					});
				
					var i = scope.attachmentFiles.length-1;

					var eid = "#afpdf"+i;
					if (type[1] != 'pdf') eid = "#afimg"+i;
					var preview = document.querySelector(eid);
					var reader  = new FileReader();

					reader.addEventListener("load", function () {
						if (type[1] == "pdf") preview.data = reader.result;
						else preview.src = reader.result;
						scope.attachmentFiles[i].eid = eid;
					}, false);

					if (file) {
						reader.readAsDataURL(file);
					};

				});

			});
			
		}
	};
		
}).factory('uploadFiles',function($q) {

	function uploadFiles() {
		
		var self = this;
		
		self.start = function(scope,callback) {

			var types = {
				"pdf": "data",
				"jpeg": "src",
				"png": "src",
			};
		
			var deferred = $q.defer();
			var promise = deferred.promise;
		
			angular.forEach(scope.documentFiles, function(item,i) {

				var $file = $(item.eid);
				var file = $($file[0]).attr(types[item.type]);

				scope.doc.files.push({file: file, type: item.type});

			});

			angular.forEach(scope.attachmentFiles, function(item,i) {

				var $file = $(item.eid);
				var file = $($file[0]).attr(types[item.type]);

				scope.doc.attachments.push({file: file, type: item.type});

			});			

			$q.all(scope.doc.files).then(function() {
				callback();
			});

			deferred.resolve();		

		};

	};

	return new uploadFiles();
	
});