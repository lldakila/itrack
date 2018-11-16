var app = angular.module('transactions',['account-module','app-module']);

app.controller('transactionsCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);

	$scope.app.list($scope);
	
	$scope.module = {
		id: 'maintenance',
		privileges: {
			show: 1,
			add: 2,
			delete: "delete_item",
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