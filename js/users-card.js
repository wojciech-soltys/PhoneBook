angular.module('main').directive('usersCard', function() {
	return {
		restrict: 'E',
		scope: {
			list: '=',
			query: '='
		},
		templateUrl : 'include/users-card.html',
		controller: function($scope, $rootScope, $element, $location, $mdDialog, usersService, informService) { 
			$scope.userLogged = localStorage.getItem('Username');
			$scope.userRole = localStorage.getItem('UserRole');

			$scope.removeUser = function(user){
				if(user.username === $scope.userLogged || $scope.userRole !== 'H') {
					informService.showAlert('Błąd', 'Nie można usunąć użytkownika');
				} else {
					var msg = 'Czy usunąć użytkownika ' + user.username + '?';
					$mdDialog.show(informService.showConfirm('Potwierdzenie', msg)).then(
						function(){
							usersService.removeUser(user)
							.success(function () {
								informService.showSimpleToast('Użykownik o nazwie ' + user.username + ' został usunięty');
								$rootScope.$emit('refresh.users.list', '');
							})
							.error(function (data, status) {
								if (status === 404) {
									informService.showAlert('Błąd', 'Nie znaleziono użytkownika ' + user.username);
								} else {
									informService.showAlert('Błąd', 'Usunięcie użytkownika nie powiodło się');
								}
								if (status === 401) {
									$rootScope.$emit('session.timeout', '');
								}
							});
					});
				}
			};

			$scope.editUser = function(user) {
				$rootScope.$emit('user.to.edit', angular.copy(user));
			};
		}
	}
});