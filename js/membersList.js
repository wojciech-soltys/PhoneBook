app.controller('membersListCtrl', ['$scope', '$rootScope', 'informService', 'membersService', 
	function ($scope, $rootScope, informService, membersService) {
		$scope.itemsExists = true;
		$scope.membersList = null;
		$scope.query = '';

		var getMembersList = function() {
			membersService.getMembersList(0)
			.success(function (data) {
				$scope.membersList = data;
				if ($scope.membersList.length == 0) {
					$scope.itemsExists = false;
				} else {
					$scope.itemsExists= true;
				}
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania listy członków');
				$scope.itemsExists = false;
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};

		getMembersList();
	}]);