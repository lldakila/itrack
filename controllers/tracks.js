var app = angular.module('tracks',['account-module','app-module']);

app.controller('tracksCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.module = {
		id: 'tracks',
		privileges: {

		}
	};		
	
});