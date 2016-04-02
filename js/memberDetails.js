app.controller('memberDetailsCtrl', ['$scope', '$rootScope', '$stateParams', '$location', 'informService', 'membersService', 
	function ($scope, $rootScope, $stateParams, $location, informService, membersService) {
		'use strict';
		$scope.member = null;
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
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania szczegółów członka');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};
		getMembersDetails();

		$scope.clearForm = function() {
			$rootScope.$emit('clear.new.payments', $scope.memberId);
		};

		$scope.moveToOld = function() {
			membersService.moveToOld($scope.memberId)
			.success(function () {
				informService.showSimpleToast('Przeniesiono członka do byłych członków');
				$scope.member.old = 1;
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd zapisu');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};

		$scope.moveToCurrent = function() {
			membersService.moveToCurrent($scope.memberId)
			.success(function () {
				informService.showSimpleToast('Przeniesiono członka do aktualnych członków');
				$scope.member.old = 0;
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd usuwania');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};

		$scope.deleteMember = function() {
			membersService.deleteMember($scope.memberId)
			.success(function () {
				informService.showSimpleToast('Usunięto członka');
				$location.path('/membersList');
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd zapisu');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};	

	}]);