app.controller('oldMembersListCtrl', ['$scope', '$rootScope', 'informService', 'membersService', 
	function ($scope, $rootScope, informService, membersService) {
		$scope.itemsExists = true;
		$scope.oldMembersList = null;
		$scope.query = '';

		var getOldMembersList = function() {
			membersService.getMembersList(1)
			.success(function (data) {
				$scope.oldMembersList = data;
				if ($scope.oldMembersList.length == 0) {
					$scope.itemsExists = false;
				} else {
					$scope.itemsExists= true;
				}
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania listy byłych członków');
				$scope.itemsExists = false;
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};
		
		getOldMembersList();
	}]);