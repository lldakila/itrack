angular.module('notifications-module', ['ngSanitize']).directive('notifications', function($interval,$timeout,$http) {		
	
	function notifications(scope) {

		$http({
		  method: 'POST',
		  url: 'handlers/notifications.php'		  
		}).then(function mySucces(response) {

			scope.notifications = angular.copy(response.data);

		}, function myError(response) {

		});

	};
	
	function dismissNotification(scope,notification) {
		
		$http({
		  method: 'POST',
		  url: 'handlers/dismiss-notification.php',
		  data: notification
		}).then(function mySucces(response) {

		}, function myError(response) {
			
		});

	};
	
	return {
		restrict: 'A',
		templateUrl: 'html/notifications.html',
		link: function(scope, element, attrs) {	

			$timeout(function() {

				$http({
				  method: 'POST',
				  url: 'handlers/access.php',
				  data: {group: scope.profile.group, mod: 'notifications', prop: 1}
				}).then(function mySucces(response) {
					
					scope.notifications = {};
					scope.notifications.count = 0;

					if (response.data.value) {

						var notification = $interval(function() {
							
							notifications(scope);

						},2000);

					};		

				},
				function myError(response) {

				
				
				});			
		
			}, 2000);				

			scope.notificationAction = function(scope,notification) {
				
				dismissNotification(scope,notification);
				
			};				

		}
	};

});