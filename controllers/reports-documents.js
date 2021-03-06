var app = angular.module('reportsDocuments',['account-module','app-module']);

app.controller('reportsDocumentsCtrl', function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
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