var app = angular.module('document',['account-module','app-module','ngRoute']);

app.controller('documentCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
	$scope.module = {
		id: 'receive_document',
		privileges: {
			show: 1,
			add: 2,
		}
	};
	
});