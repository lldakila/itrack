var app = angular.module('documents',['account-module','app-module']);

app.controller('documentsCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.module = {
		id: 'documents',
		privileges: {
			show: 1,
			view: 2,
			edit: 3,
			delete: "delete_document",
		}
	};
	
});

/* app.filter('orderObjectBy', function() {
	return function(items, field, reverse) {
		var filtered = [];
		angular.forEach(items, function(item) {
			filtered.push(item);
		});
		filtered.sort(function(a, b) {
			return (a[field] > b[field] ? 1 : -1);
		});
		if (reverse) filtered.reverse();
		return filtered;
	};
}); */