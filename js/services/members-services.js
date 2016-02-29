angular.module('main.membersFactory', [])
.factory('membersService', ['$http', function($http) {
	'use strict';
	var membersService={};

	membersService.getMembersList = function() {
		return $http({
			method: 'post',
			url: 'getMembersList',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
			}
		});
	};

	membersService.getMentors = function() {
		return $http({
			method: 'post',
			url: 'getMentors',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
			}
		});
	};

	membersService.saveMember = function(member) {
		return $http({
			method: 'post',
			url: 'saveMember',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				firstName: member.firstName,
				lastName: member.lastName,
				accessionDate: member.accessionDate,
				phone: member.phone,
				privateEmail: member.privateEmail,
				aegeeEmail: member.aegeeEmail,
				birthDate: member.birthDate,
				cardNumber: member.cardNumber,
				declaration: member.declaration,
				connectedToList: member.connectedToList,
				mentorId: member.mentorId
			}
		});
	};

	return membersService;
}]);