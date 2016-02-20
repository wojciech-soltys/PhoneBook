app.controller('membersListCtrl', ['$scope', 'informService', 'membersService', 
	function ($scope, informService, membersService) {
	$scope.itemsExists = true;
	$scope.membersList = null;
	$scope.query = '';

	var getMembersList = function() {
		membersService.getMembersList($scope.event_edition)
		.success(function (data) {
			$scope.membersList = data;
			if ($scope.membersList.length == 0) {
				$scope.itemsExists = false;
			} else {
				$scope.itemsExists= true;
			}
		})
		.error(function () {
			informService.showSimpleToast('Błąd pobrania listy członków');
			$scope.itemsExists = false;
		});
	};

	getMembersList();
}]);