var app = angular.module('receive',['account-module','app-module']);

app.controller('receiveCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.module = {
		id: 'receive',
		privileges: {
			show: 1,
			receive: 2,
		}
	};
	
});

/* app.filter('pagination', function() {
	  return function(input, currentPage, pageSize) {
	    if(angular.isArray(input)) {
	      var start = (currentPage-1)*pageSize;
	      var end = currentPage*pageSize;
	      return input.slice(start, end);
	    }
	  };
}); */