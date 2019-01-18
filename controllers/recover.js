var app = angular.module('recoverPassword',['app-module']);

app.controller('recoverPasswordCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
});