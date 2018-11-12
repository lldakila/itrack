var app = angular.module('documents',['account-module','app-module']);

app.controller('documentsCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	app.list($scope);
	
	$scope.module = {
		id: 'documents',
		privileges: {
			show: 1,
			view: 2,
			edit: 3,
			delete: "delete_document",
		}
	};
	
});

app.filter('pagination', function() {
	  return function(input, currentPage, pageSize) {
	    if(angular.isArray(input)) {
	      var start = (currentPage-1)*pageSize;
	      var end = currentPage*pageSize;
	      return input.slice(start, end);
	    }
	  };
});