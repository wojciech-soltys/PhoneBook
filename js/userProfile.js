app.controller('userProfileCtrl',['$scope', '$rootScope', 'usersService', 'informService',
	function ($scope, $rootScope, usersService, informService) {
		'use strict';
		$scope.user = null;
		$scope.changePassword = 0;
		var privateEmailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

		var getUserProfile = function() {
			usersService.getUserProfile()
			.success(function (data) {
				$scope.user = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania danych o profilu użytkownika');
			});
		};

		getUserProfile();

		var validation = function(form) {
			if (form.$invalid) {
				informService.showAlert('Błąd', 'Wypełnij poprawnie formularz');
				return false;
			} else if(!privateEmailRegex.test($scope.user.privateEmail)) {
				informService.showAlert('Błąd', 'Wypełnij poprawnie pole e-mail');
				return false;
			} else if ($scope.changePassword && $scope.user.password !== $scope.user.confirmPassword) {
				informService.showAlert('Błąd', 'Nowe hasło i potwierdzone hasło nie są identyczne');
				return false;
			} else {
				return true;
			}
		};

		$scope.save= function(form) {
			if (validation(form)) {
				usersService.setUserProfile($scope.user)
				.success(function (data) {
					$scope.user = data;
					var msg = data.firstName + ' ' + data.lastName;
					$rootScope.$emit('edit.profile', msg);
					informService.showSimpleToast('Dane o użytkowniku zostały zaktualizowane');
				})
				.error(function (data) {
					switch (data.code) {
						case 'privateEmail':
							informService.showAlert('Błąd', 'Wypełnij poprawnie pole e-mail');
							break;
						case 'password':
							informService.showAlert('Błąd', 'Wypełnij poprawnie pole hasło');
							break;
						default:
							informService.showAlert('Błąd', 'Dane nie zostały zaktualizowane');
					}
				});
			}
		};

	}]);