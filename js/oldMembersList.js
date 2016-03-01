app.controller('oldMembersListCtrl', ['$scope', 'informService', 'membersService', 
	function ($scope, informService, membersService) {
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
		.error(function () {
			informService.showSimpleToast('Błąd pobrania listy byłych członków');
			$scope.itemsExists = false;
		});
	};
	
	getOldMembersList();
}]);