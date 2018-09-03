var app = angular.module('login',['login-module']);

app.controller('loginCtrl',function($scope,loginService) {
	
	$scope.views = {};
	$scope.account = {};
	
	$scope.login = loginService.login;	
	
});