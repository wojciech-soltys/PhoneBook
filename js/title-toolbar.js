angular.module('main').directive('titleToolbar', [function() {
	return {
		restrict: 'E',
		scope: {
			title: '@',
			toggleLeft: '&',
			userInfo: '=',
			logout: '&'
		},
		templateUrl : 'include/title-toolbar.html'

	}
}]);