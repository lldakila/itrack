angular.module('file-uploader',[]).directive('fileModel', function ($parse) {
	return {
	   restrict: 'A',
	   link: function(scope, element, attrs) {
		  var model = $parse(attrs.fileModel);
		  var modelSetter = model.assign;
		  
		  element.bind('change', function() {
			 scope.$apply(function(){
				modelSetter(scope, element[0].files[0]);
			 });
		  });

		  // scope.$watch(attrs.fileModel, function(file) {
			// $('#'+element['context']['id']).val(null);
		  // });
	   }
	};
}).service('fileUpload', function ($http) {
	this.uploadFileToUrl = function(file, uploadUrl, scope) {
		
	   var fd = new FormData();
	   fd.append('file', file);

        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", uploadProgress, false);
        xhr.addEventListener("load", uploadComplete, false);
        xhr.open("POST", uploadUrl);
		
        scope.progressVisible = true;
		
        xhr.send(fd);
	   
		// upload progress
		function uploadProgress(evt) {
			scope.progress.show = true;
			scope.$apply(function(){
				scope.progress.status = 0;			
				if (evt.lengthComputable) {
					scope.progress.status = Math.round(evt.loaded * 100 / evt.total);
				} else {
					scope.progress.status = 'unable to compute';		
				}
			});
		}

		function uploadComplete(evt) {
			/* This event is raised when the server send back a response */
			scope.$apply(function() {

				// scope.progress.show = false;
				scope.app.revisions.list(scope);

			});		

			// $('#file_revision').val(null);
		}

	}
});