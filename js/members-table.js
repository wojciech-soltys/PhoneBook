angular.module('main').directive('membersTable', function() {
	return {
		restrict: 'E',
		scope: {
			list: '=',
			query: '=',
			old: '='
		},
		templateUrl : 'include/members-table.html',
		controller: function($scope, $element, $location) { 
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
		}
	}
});