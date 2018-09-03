angular.module('account-module', ['bootstrap-modal']).directive('dropDown', function() {

	return {
		restrict: 'A',
		templateUrl: 'angular/modules/account/account.html'
	};
	
}).directive('accountProfile',function($http,$window) {
	
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			
			$http({
			  method: 'POST',
			  url: 'angular/modules/account/account-profile.php'
			}).then(function mySucces(response) {

				scope.profile = response.data;
				
			},
			function myError(response) {
				
				$window.location.href = 'login.html';
				
			});			
			
		}
	};
		
}).directive('logoutAccount', function($http,$window,bootstrapModal) {
	
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			
			var onOk = function() {
				
				$window.location.href = 'angular/modules/login/logout.php';
				
			};
			
			element.bind('click', function() {
					
				bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to logout?',onOk,function() {});

			});
			
		}
		
	};

});