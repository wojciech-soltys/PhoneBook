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
				currentPassword: user.currentPassword
			}
		});
	};

	usersService.setNewUser = function(user) {
		return $http({
			method: 'post',
			url: 'setNewUser',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				password: user.password,
				_username: user.username,
				memberId: user.memberId
			}
		});
	};

	usersService.removeUser = function(user) {
		return $http({
			method: 'post',
			url: 'removeUser',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				id: user.id,
				_username: user.username
			}
		});
	};

	usersService.setNewPassword = function(user) {
		return $http({
			method: 'post',
			url: 'setNewPassword',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				memberId: user.memberId,
				password: user.password,
			}
		});
	};

	return usersService;
}]);