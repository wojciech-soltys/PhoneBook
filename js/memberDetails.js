app.controller('memberDetailsCtrl', ['$scope', '$stateParams', 'informService', 'membersService', 
	function ($scope, $stateParams, informService, membersService) {

		$scope.memberDetails = null;
		$scope.memberId = $stateParams.id;

		var getMembersDetails = function() {
			membersService.getMembersDetails($scope.memberId)
			.success(function (data) {
				$scope.membersDetails = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania szczegółów członka');
			});
		};

		getMembersDetails();
	}]);