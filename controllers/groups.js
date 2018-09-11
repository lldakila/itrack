var app = angular.module('groups',['account-module','app-module','ngRoute']);

app.controller('groupsCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
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