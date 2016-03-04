app.controller('usersListCtrl', ['$scope', '$rootScope', 'informService', 'usersService', 
	function ($scope, $rootScope, informService, usersService) {
		$scope.itemsExists = true;
		$scope.usersList = null;
		$scope.query = '';

		var getUsersList = function() {
			usersService.getUsersList()
			.success(function (data) {
				$scope.usersList = data;
				if ($scope.usersList.length == 0) {
					$scope.itemsExists = false;
				} else {
					$scope.itemsExists= true;
				}
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania listy użytkowników');
				$scope.itemsExists = false;
			});
		};

		getUsersList();
		
		$scope.$on('new.user', function() {
			getUsersList();
		});

		$rootScope.$on('edit.user', function () {
			getUsersList();	
		});

		$scope.clearForm = function() {
			$rootScope.$emit('clear.new.user', '');
		};
	}]);