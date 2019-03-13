angular.module('notifications-module', ['ngSanitize']).directive('notifications', function($interval,$timeout,$http,$window) {		
	
	function notifications(scope) {

		var source = new EventSource("/handlers/notifications.php");

		source.onmessage = function(event) {
			
			var notifications = JSON.parse(event.data);
			scope.notifications = notifications;
			scope.$apply();

		};
		
		/* $http({
		  method: 'GET',
		  url: '/api/notifications/fetch'
		}).then(function mySucces(response) {

			scope.notifications = angular.copy(response.data);

		}, function myError(response) {

		}); */

	};
	
	function hideNotification(scope,notification) {

		$http({
		  method: 'GET',
		  url: '/api/notifications/hide/'+notification.id,
		}).then(function mySucces(response) {

			$window.location.href = notification.url;		
		
		}, function myError(response) {
			
		});

	};
	
	function hideNotifySeen(scope,notification) {
		
		$http({
		  method: 'POST',
		  url: '/api/notifications/hide/seen/'+notification.id,
		  data: notification
		}).then(function mySucces(response) {

			// $window.location.href = notification.url;		
		
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
					
						/* var notification = $interval(function() {
							
							notifications(scope);

						},1000); */

					};		

				},
				function myError(response) {

				
				
				});			
		
			}, 1000);				

			scope.notificationAction = function(scope,notification) {

				if (notification.inform_seen == "") hideNotification(scope,notification);
				else hideNotifySeen(scope,notification);
				
			};

		}
	};

});