app.controller('reportsCtrl', ['$scope', '$rootScope', '$stateParams', 'informService', 'membersService', 
	function ($scope, $rootScope, $stateParams, informService, membersService) {

		$scope.userRole = localStorage.getItem('UserRole');

		$scope.reportStructure = {};
		

		$scope.generateReport = function(form) {

		};

	}]);