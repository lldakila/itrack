var app = angular.module('action',['account-module','app-module','ngRoute']);

app.controller('actionCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
	$scope.module = {
		id: 'action',
		privileges: {
			show: 1,
			add: 2,
		}
	};
	
});