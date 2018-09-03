var app = angular.module('dashboard',['account-module','dashboard-module','notifications-module']);

app.controller('dashboardCtrl',function($scope,dashboard) {
	
	$scope.views = {};
	
	$scope.module = {
		id: 'dashboard',
		privileges: {

		}
	};

	dashboard.load($scope);
	
});