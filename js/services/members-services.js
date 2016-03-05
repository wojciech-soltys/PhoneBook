angular.module('main.membersFactory', [])
.factory('membersService', ['$http', function($http) {
	'use strict';
	var membersService={};

	membersService.getMembersList = function(old) {
		return $http({
			method: 'post',
			url: 'getMembersList',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				old: old
			}
		});
	};

	membersService.setDeclaration = function(member) {
		return $http({
			method: 'post',
			url: 'setDeclaration',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				member_id: member.id,
				declaration: member.declaration
			}
		});
	};

	membersService.setAegeeEmail = function(member) {
		return $http({
			method: 'post',
			url: 'setAegeeEmail',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				member_id: member.id,
				aegeeEmail: member.aegeeEmail
			}
		});
	};
	
	membersService.setConnectedToList = function(member) {
		return $http({
			method: 'post',
			url: 'setConnectedToList',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				member_id: member.id,
				connectedToList: member.connectedToList
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


	membersService.getMembersDetails = function(memberId) {
		return $http({
			method: 'post',
			url: 'getMembersDetails',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				member_id: memberId
			}
		});
	};

	return membersService;
}]);