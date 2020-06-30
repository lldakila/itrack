var app = angular.module('track',['app-module']);

app.controller('trackCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
});