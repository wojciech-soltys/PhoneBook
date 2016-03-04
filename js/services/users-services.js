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


	return usersService;
}]);