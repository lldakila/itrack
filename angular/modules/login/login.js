angular.module('login-module', []).service('loginService', function($http, $window) {
	
	this.login = function(scope) {
		
		scope.views.incorrect = false;

		$http({
		  method: 'POST',
		  url: 'angular/modules/login/login.php',
		  data: scope.account
		}).then(function mySucces(response) {

			if (response.data['login']) {
				scope.views.incorrect = false;
				$window.location.href = 'index.html';
			} else {
				scope.views.incorrect = true;
			}

		},
		function myError(response) {

		});
		
	};
	
});

var app = angular.module('login',['login-module']);

app.controller('loginCtrl',function($scope,loginService) {
	
	$scope.views = {};
	$scope.account = {};
	
	$scope.login = loginService.login;	
	
});