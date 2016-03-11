app.controller('statisticsCtrl', ['$scope', '$rootScope', 'membersService', 'informService',
	function($scope, $rootScope, membersService, informService){
		'use strict';
		$scope.statistics = {};

		var getStatistics = function() {
			membersService.getStatistics()
			.success(function (data) {
				$scope.statistics = data;
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania statystyk');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};

		getStatistics();

}]);