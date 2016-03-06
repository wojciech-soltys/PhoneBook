angular.module('main').directive('membersTable', function() {
	return {
		restrict: 'E',
		scope: {
			list: '=',
			query: '=',
			old: '='
		},
		templateUrl : 'include/members-table.html',
		controller: function($scope, $rootScope, $element, $location, membersService, informService) { 
			$scope.checkExpirationDate = function(member) {
				if ($scope.old || member.type === 'H') {
					return false;
				}
				if (member.expirationDate === null || member.expirationDate === '') {
					member.expirationDate = '';
					return true;
				}
				var expDate = new Date(member.expirationDate);
				var now = new Date();
				if (expDate < now)
					return true;
				return false;
			};

			$scope.changeDeclaration = function(member) {
				membersService.setDeclaration(member)
				.success(function () {
					informService.showSimpleToast('Zmiana została zapisana');
				})
				.error(function (data, status) {
					informService.showSimpleToast('Błąd zapisu');
					if (status === 401) {
						$rootScope.$emit('session.timeout', '');
					}
				});
			};

			$scope.changeAegeeEmail = function(member) {
				membersService.setAegeeEmail(member)
				.success(function () {
					informService.showSimpleToast('Zmiana została zapisana');
				})
				.error(function (data, status) {
					informService.showSimpleToast('Błąd zapisu');
					if (status === 401) {
						$rootScope.$emit('session.timeout', '');
					}
				});
			};

			$scope.changeConnectedToList = function(member) {
				membersService.setConnectedToList(member)
				.success(function () {
					informService.showSimpleToast('Zmiana została zapisana');
				})
				.error(function (data, status) {
					informService.showSimpleToast('Błąd zapisu');
					if (status === 401) {
						$rootScope.$emit('session.timeout', '');
					}
				});
			};
		}
	}
});