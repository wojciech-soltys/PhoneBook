var myApp=angular.module('login', ['login.loginFactory'])
.controller('loginCtrl', ['$scope', '$http', '$window', 'loginService', function ($scope, $http, $window, loginService) {
	'use strict';
	$scope.errorMessage = '';
	function init() {
		if (localStorage.getItem('Username') != null) {
			loginService.isUserLogged()
			.success(function (data, status, headers, config) {
				if(data.isLoggedIn) {
					$window.location.href = data.url;
				}
			})
			.error(function (data, status, headers, config) {
				localStorage.removeItem('Username');
				localStorage.removeItem('TimeStamp');
				localStorage.removeItem('SessionID');
				localStorage.removeItem('UserRole');
			});
		}
	};

	init();

	$scope.login = function() {
		loginService.login($scope.user.username, $scope.user.password)
		.success(function (data) {
			localStorage.setItem('TimeStamp', new Date().getTime());
			localStorage.setItem('SessionID', data.session);
			localStorage.setItem('Username', $scope.user.username );
			localStorage.setItem('UserRole', data.role);
			$window.location.href = data.url;
			$scope.errorMessage = '';
		})
		.error(function () {
			$scope.errorMessage = 'Niepoprawna próba logowania. Błędna nazwa użytkownika lub hasło.';
		});
	};
}])
.directive('myEnter', function () {
	'use strict';
	return function (scope, element, attrs) {
		element.bind('keydown keypress', function (event) {
			if(event.which === 13) {
				scope.$apply(function () {
					scope.$eval(attrs.myEnter);
				});
				event.preventDefault();
			}
		});
	};
});


