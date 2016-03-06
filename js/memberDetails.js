app.controller('memberDetailsCtrl', ['$scope', '$stateParams', 'informService', 'membersService', 
	function ($scope, $stateParams, informService, membersService) {

		$scope.memberDetails = null;
		$scope.memberId = $stateParams.id;

		$scope.types = [
		{id: 'C', name: 'Członek zwyczajny'},
		{id: 'Z', name: 'Zarząd'},
		{id: 'R', name: "Komisja Rewizyjna"},
		{id: 'K', name: 'Koordynator grupy roboczej'},
		{id: 'H', name: 'Członek honorowy'}];

		var getMembersDetails = function() {
			membersService.getMemberDetails($scope.memberId)
			.success(function (data) {
				$scope.memberDetails = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania szczegółów członka');
			});
		};

		var getMentors = function() {
			membersService.getMentors()
			.success(function (data) {
				$scope.mentors = data;
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania listy mentorów');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};

		var init = function() {
			getMembersDetails();
			getMentors();
		};

		init();

	}]);