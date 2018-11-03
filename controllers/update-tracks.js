var app = angular.module('updateTracks',['account-module','app-module']);

app.controller('updateTracksCtrl',function($scope,app) {
	
	$scope.app = app;
	
	$scope.app.data($scope);
	
	$scope.module = {
		id: 'update_tracks',
		privileges: {
			show: 1,
			view: 2,
			update: 3,
		}
	};
	
});

app.filter('pagination', function() {
	  return function(input, currentPage, pageSize) {
	    if(angular.isArray(input)) {
	      var start = (currentPage-1)*pageSize;
	      var end = currentPage*pageSize;
	      return input.slice(start, end);
	    }
	  };
});