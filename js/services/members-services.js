angular.module('main.membersFactory', [])
.factory('membersService', ['$http', function($http) {
	'use strict';
	var membersService={};

	membersService.getMembersList = function(username, password) {
		return $http({
			method: 'post',
			url: 'getMembersList',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
			}
		});
	};

	return membersService;
}]);