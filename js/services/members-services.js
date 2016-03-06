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

	membersService.getMembersShortList = function() {
		return $http({
			method: 'post',
			url: 'getMembersShortList',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
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
				mentorId: member.mentorId,
				type: member.type
			}
		});
	};

	membersService.changeMember = function(member) {
		return $http({
			method: 'post',
			url: 'changeMember',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				id: member.id,
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
				mentorId: member.mentorId,
				type: member.type
			}
		});
	};

	membersService.getMemberDetails = function(memberId) {
		return $http({
			method: 'post',
			url: 'getMemberDetails',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				member_id: memberId
			}
		});
	};

	membersService.setNewPayment = function(payment) {
		return $http({
			method: 'post',
			url: 'setNewPayment',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID'),
				memberId: payment.memberId,
				paymentDate: payment.paymentDate,
				type: payment.type,
				expirationDate: payment.expirationDate,
				amount: payment.amount
			}
		});
	};

	return membersService;
}]);