app.controller('userProfileCtrl',['$scope', '$rootScope', 'usersService', 'informService',
	function ($scope, $rootScope, usersService, informService) {
		'use strict';
		$scope.user = null;
		$scope.changePassword = 0;

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
					informService.showSimpleToast('Profil użytkownika został zaktualizowany');
					form.$setPristine();
					form.$setUntouched();
					$scope.changePassword = 0;
					getUserProfile();
				})
				.error(function (data, status) {
					switch (data.code) {
						case 'currentPassword':
							informService.showAlert('Błąd', 'Podano błędne aktualne hasło');
							break;
						case 'password':
							informService.showAlert('Błąd', 'POdano błędne nowe hasło');
							break;
						default:
							informService.showAlert('Błąd', 'Dane nie zostały zaktualizowane');
					}
					if (status === 401) {
						$rootScope.$emit('session.timeout', '');
					}
				});
			}
		};

	}]);