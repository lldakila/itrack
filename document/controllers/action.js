var app = angular.module('action',['account-module','app-module','ngRoute']);

app.controller('actionCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.app.startup($scope);	
	
	$scope.module = {
		id: 'update_tracks',
		privileges: {
			show: 1,
			view: 2,
			update: 3,
			transit: 4,
			release: 5,
			comment: 6,
			add_revision: 7,
			edit_revision: 8,
			delete_revision: "delete_revision",
		}
	};
	
});