var app = angular.module('profileSettings',['account-module','app-module']);

app.controller('profileSettingsCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	/* $scope.module = {
		id: 'maintenance',
		privileges: {
			show: 1,
			add: 2,
			delete: 3,
		}
	}; */	

});