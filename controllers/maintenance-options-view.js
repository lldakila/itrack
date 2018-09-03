var app = angular.module('maintenanceOptionsList',['account-module','app-module']);

app.controller('maintenanceOptionsCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);

	$scope.app.list($scope);
	
	$scope.module = {
		id: 'maintenance',
		privileges: {
			show: 1,
			add: 2,
			delete: 3,
		}
	};	

});