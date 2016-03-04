app.controller('memberDetailsCtrl', ['$scope', 'informService', 'membersService', 
	function ($scope, informService, membersService) {

		$scope.memberDetails = null;

		var getMembersDetails = function() {
			membersService.getMembersDetails($scope.memberId)
			.success(function (data) {
				$scope.membersDetails = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania listy członków');
				$scope.itemsExists = false;
			});
		};

		//getMembersDetails();
	}]);