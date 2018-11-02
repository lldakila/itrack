var app = angular.module('document',['account-module','app-module','ngRoute','notifications-module']);

app.controller('documentCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
	$scope.module = {
		id: 'view_document',
		privileges: {
			show: 1,
			add: 2,
		}
	};
	
});