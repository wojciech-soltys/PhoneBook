app.controller('memberEditCtrl', ['$scope', '$stateParams', 'informService', 'membersService', 
	function ($scope, $stateParams, informService, membersService) {
		$scope.member = $stateParams.id;
		if (angular.isUndefined($scope.member)) {
			$scope.pageTitle = "Nowy członek";
		} else {
			$scope.pageTitle = "Edycja danych członka";
		}

	}]);