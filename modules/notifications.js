angular.module('notifications-module', ['ngSanitize']).directive('notifications', function($interval,$timeout,$http) {		
	
	function notifications(scope) {

		$http({
		  method: 'GET',
		  url: '/api/notifications/fetch'
		}).then(function mySucces(response) {

			scope.notifications = angular.copy(response.data);

		}, function myError(response) {

		});

	};
	
	function dismissNotification(scope,notification) {
		
		$http({
		  method: 'POST',
		  url: '/handlers/dismiss-notification.php',
		  data: notification
		}).then(function mySucces(response) {

		}, function myError(response) {
			
		});

	};
	
	return {
		restrict: 'A',
		templateUrl: '/html/notifications.html',
		link: function(scope, element, attrs) {	

			$timeout(function() {

				$http({
				  method: 'POST',
				  url: '/handlers/access.php',
				  data: {group: scope.profile.group, mod: 'notifications', prop: 1}
				}).then(function mySucces(response) {
					
					scope.notifications = {};
					scope.notifications.count = 0;

					if (response.data.value) {

						notifications(scope);
					
						var notification = $interval(function() {
							
							notifications(scope);

						},1000);

					};		

				},
				function myError(response) {

				
				
				});			
		
			}, 1000);				

			scope.notificationAction = function(scope,notification) {
				
				dismissNotification(scope,notification);
				
			};				

		}
	};

});