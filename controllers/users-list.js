var app = angular.module('profileList',['account-module','app-module']);

app.controller('profileListCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);

	$scope.app.list($scope);
	
	$scope.module = {
		id: 'accounts',
		privileges: {
			show: 1,
			add: 2,
			edit: 3,
			delete: 4,
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