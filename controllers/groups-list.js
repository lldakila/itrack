var app = angular.module('groupsList',['account-module','app-module']);

app.controller('groupsListCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);

	$scope.app.list($scope);
	
	$scope.module = {
		id: 'groups',
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