app.controller('memberDetailsCtrl', ['$scope', '$rootScope', '$stateParams', 'informService', 'membersService', 
	function ($scope, $rootScope, $stateParams, informService, membersService) {
		$scope.member = {};
		$scope.memberId = $stateParams.id;
		$scope.userRole = localStorage.getItem('UserRole');

		$scope.types = [
		{id: 'C', name: 'Członek zwyczajny'},
		{id: 'Z', name: 'Zarząd'},
		{id: 'R', name: "Komisja Rewizyjna"},
		{id: 'K', name: 'Koordynator grupy roboczej'},
		{id: 'H', name: 'Członek honorowy'}];

		var getMembersDetails = function() {
			membersService.getMemberDetails($scope.memberId)
			.success(function (data) {
				$scope.member = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania szczegółów członka');
			});
		};
		getMembersDetails();

		$scope.clearForm = function() {
			$rootScope.$emit('clear.new.payments', $scope.memberId);
		}
		

	}]);