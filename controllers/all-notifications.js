var app = angular.module('allNotifications',['account-module','all-notifications-module','notifications-module']);

app.controller('allNotificationsCtrl',function($scope,allNotifications) {
	
	$scope.views = {};
	
	$scope.formHolder = {};
	
	$scope.filter = {};
	
	$scope.module = {
		id: 'all_notifications',
		privileges: {

		}
	};

	$scope.allNotifications = allNotifications;
	
	$scope.allNotifications.load($scope);
	
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