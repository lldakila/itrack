var app = angular.module('outgoing',['account-module','app-module']);

app.controller('outgoingCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);	

	$scope.module = {
		id: 'outgoing',
		privileges: {

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