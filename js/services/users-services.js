angular.module('main.usersFactory', [])
.factory('usersService', ['$http', function($http) {
	'use strict';
	var usersService={};

	usersService.getUsersList = function() {
		return $http({
			method: 'post',
			url: 'getUsersList',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID')
			}
		});
	};

	usersService.getUserProfile = function() {
		return $http({
			method: 'post',
			url: 'getUserProfile',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID')
			}
		});
	};

	usersService.setUserProfile = function(user) {
		return $http({
			method: 'post',
			url: 'setUserProfile',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				password: user.password,
				privateEmail: user.privateEmail,
				oldPassword: user.oldPassword
			}
		});
	};

	return usersService;
}]);