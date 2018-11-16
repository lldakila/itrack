var app = angular.module('account',['account-module','app-module','ngRoute']);

app.controller('accountCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
	$scope.module = {
		id: 'accounts',
		privileges: {
			show: 1,
			add: 2,
			edit: 3,
			delete: "delete_account",
		}
	};	
});