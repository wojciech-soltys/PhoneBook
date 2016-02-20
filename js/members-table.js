angular.module('main').directive('membersTable', function() {
	return {
		restrict: 'E',
		scope: {
			list: '=',
			query: '='
		},
		templateUrl : 'include/members-table.html'
	}
});