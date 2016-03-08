var app=angular.module('main', ['ui.router', 'ui.bootstrap', 'ngMaterial', 'login.loginFactory', 
	'main.inform', 'main.membersFactory', 'main.usersFactory']);

app.controller('navigationCtrl', ['$scope', '$rootScope', '$http', '$timeout', 
	'$location', '$mdSidenav', '$window', 'loginService', 'informService', 
	function ($scope, $rootScope, $http, $timeout, $location, $mdSidenav, $window, 
		loginService, informService) {
		'use strict';
		$scope.userInfo = '';

		var clearLocStorageGoLogin = function() {
			localStorage.removeItem('Username');
			localStorage.removeItem('TimeStamp');
			localStorage.removeItem('SessionID');
			localStorage.removeItem('UserRole');
			$window.location.href = 'login.html';
		};

		$scope.logout = function() {
			loginService.logout()
			.success(function () {
				clearLocStorageGoLogin();
			})
			.error(function () {
				clearLocStorageGoLogin();
			});
		};
		
		var checkSession = function() {
			loginService.isUserLogged()
			.success(function (data) {
				if(data.isLoggedIn) {
					$scope.userInfo = data.firstName + ' ' + data.lastName;
				} else {
					clearLocStorageGoLogin();
				}
			})
			.error(function () {
				clearLocStorageGoLogin();
			});
		};

		$scope.userRole = localStorage.getItem('UserRole');

		checkSession();

		$rootScope.$on('edit.profile', function (event, value) {
			$scope.userInfo = value;
		});

		$rootScope.$on('session.timeout', function () {
			checkSession();
		});

		$scope.closeLeft = function () {
			$mdSidenav('left').close()
			.then(function () {
			});
		};

		$scope.closeRight = function () {
			$mdSidenav('right').close()
			.then(function () {
			});
		};

		$scope.toggleLeft = buildDelayedToggler('left');
		$scope.toggleRight = buildToggler('right');

		$scope.isOpenRight = function(){
			return $mdSidenav('right').isOpen();
		};

		$scope.isActive = function (path) {
			if ($location.path().substr(0, path.length) === path) {
				return true;
			} else {
				return false;
			}
		}

		function debounce(func, wait) {
			var timer;
			return function debounced() {
				var context = $scope,
				args = Array.prototype.slice.call(arguments);
				$timeout.cancel(timer);
				timer = $timeout(function() {
					timer = undefined;
					func.apply(context, args);
				}, wait || 10);
			};
		}

		function buildDelayedToggler(navID) {
			return debounce(function() {
				$mdSidenav(navID)
				.toggle()
				.then(function () {

				});
			}, 200);
		}

		function buildToggler(navID) {
			return function() {
				$mdSidenav(navID)
				.toggle()
				.then(function () {

				});
			}
		};

	}]);

app.config(function($stateProvider, $urlRouterProvider) {
	'use strict';
	$urlRouterProvider.otherwise('/membersList');
	$stateProvider
	.state('membersList', {
		url: '/membersList',
		views: {
			'contentView': { templateUrl: 'include/membersList.html' },
			'rightView': { templateUrl: 'include/empty.html' }
		}
	})
	.state('memberDetails', {
		url: '/memberDetails?id',
		views: {
			'contentView': { templateUrl: 'include/memberDetails.html' },
			'rightView': { templateUrl: 'include/paymentNew.html' }
		}
	})
	.state('oldMembersList', {
		url: '/oldMembersList',
		views: {
			'contentView': { templateUrl: 'include/oldMembersList.html' },
			'rightView': { templateUrl: 'include/empty.html' }
		}
	})
	.state('memberEdit', {
		url: '/memberEdit?id',
		views: {
			'contentView': { templateUrl: 'include/memberEdit.html' },
			'rightView': { templateUrl: 'include/empty.html' }
		}
	})
	.state('usersList', {
		url: '/usersList',
		views: {
			'contentView': { templateUrl: 'include/usersList.html' },
			'rightView': { templateUrl: 'include/userEdit.html' }
		}
	})
	.state('userProfile', {
		url: '/userProfile',
		views: {
			'contentView': { templateUrl: 'include/userProfile.html' },
			'rightView': { templateUrl: 'include/empty.html' }
		}
	});
});

app.config(function($mdDateLocaleProvider) {
	$mdDateLocaleProvider.months = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień',
	'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik',
	'Listopad', 'Grudzień'];
	$mdDateLocaleProvider.shortMonths = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień',
	'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik',
	'Listopad', 'Grudzień'];
  	$mdDateLocaleProvider.days = ['niedziela', 'poniedziałek', 'wtorek', 'środa', 
  	'czwartek', 'piątek', 'sobota'];
  	$mdDateLocaleProvider.shortDays = ['niedz', 'pon', 'wt', 'śr', 'czw', 'pt', 'sob'];
  	$mdDateLocaleProvider.firstDayOfWeek = 1;
  	$mdDateLocaleProvider.msgCalendar = 'Kalendarz';
  	$mdDateLocaleProvider.msgOpenCalendar = 'Otwórz kalendarz';
});