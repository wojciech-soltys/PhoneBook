app.controller('userEditCtrl',['$scope', '$rootScope', 'usersService', 'membersService', 'informService',
	function ($scope, $rootScope, usersService, membersService,  informService) {
		'use strict';
		$scope.membersList = null;
		$scope.changePassword = 0;
		$scope.user = {};

		var getMembersShortList = function() {
			membersService.getMembersShortList()
			.success(function (data) {
				$scope.membersList = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania listy członków');
			});
		};

		getMembersShortList();

		$scope.setUsername = function(member) {
			$scope.user.username = member.firstName.toLowerCase() + '.' + member.lastName.toLowerCase();
		};


	}]);