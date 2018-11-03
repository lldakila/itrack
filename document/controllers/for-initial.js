var app = angular.module('documentInitial',['account-module','app-module','ngRoute']);

app.controller('documentInitialCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
	$scope.module = {
		id: 'for_initial',
		privileges: {
			show: 1,
		}
	};
	
});