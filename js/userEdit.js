app.controller('userEditCtrl',['$scope', '$rootScope', 'usersService', 'membersService', 'informService',
	function ($scope, $rootScope, usersService, membersService,  informService) {
		'use strict';
		$scope.membersList = null;
		$scope.isEdit = false;
		$scope.title = 'Nowy użytkownik';
		$scope.user = {};

		var getMembersShortList = function() {
			membersService.getMembersShortList()
			.success(function (data) {
				$scope.membersList = data;
			})
			.error(function () {
				informService.showSimpleToast('Błąd pobrania listy członków');
			});
		};

		$rootScope.$on('refresh.users.list', function () {
			getMembersShortList();
		});

		$rootScope.$on('user.to.edit', function (event, value) {
			$scope.reset($scope.userEditForm);
			$scope.isEdit = true;
			$scope.title = 'Zmiana hasła';
			$scope.user = value;
			$scope.toggleRight();
		});

		$rootScope.$on('clear.edit.user', function (event, value) {
			$scope.reset($scope.userEditForm);
		});

		$scope.closeRight();
		getMembersShortList();

		$scope.setUsername = function(member) {
			var username = member.firstName.toLowerCase() + '.' + member.lastName.toLowerCase();
			username = username.replace(/ą/g, 'a');
			username = username.replace(/ć/g, 'c');
			username = username.replace(/ę/g, 'e');
			username = username.replace(/ł/g, 'l');
			username = username.replace(/ń/g, 'n');
			username = username.replace(/ó/g, 'o');
			username = username.replace(/ś/g, 's');
			username = username.replace(/ź/g, 'z');
			username = username.replace(/ż/g, 'z');
			$scope.user.username = username;
		};

		var validation = function(form) {
			if (form.$invalid) {
				informService.showAlert('Błąd', 'Wypełnij poprawnie formularz');
				return false;
			} else if ($scope.user.password !== $scope.user.confirmPassword) {
				informService.showAlert('Błąd', 'Nowe hasło i potwierdzone hasło nie są identyczne');
				return false;
			} else {
				return true;
			}
		};

		$scope.save = function(form) {
			if (validation(form)) {
				usersService.setNewUser($scope.user)
				.success(function (data) {
					informService.showSimpleToast('Użytkownik został utworzony');
					form.$setPristine();
					form.$setUntouched();
					$rootScope.$emit('refresh.users.list', '');
					$scope.close(form);
				})
				.error(function (data, status) {
					switch (data.code) {
						case 'memberId':
							informService.showAlert('Błąd', 'Nie przypisano konta do członka');
							break;
						case 'password':
							informService.showAlert('Błąd', 'Podano błędne nowe hasło');
							break;
						case 'username':
							informService.showAlert('Błąd', 'Podano błędną nazwę użytkownika');
							break;
						default:
							informService.showAlert('Błąd', 'Dane nie zostały zapisane');
					}
					if (status === 401) {
						$rootScope.$emit('session.timeout', '');
					}
				});
			}
		};

		$scope.saveChanges = function(form) {
			if (validation(form)) {
				usersService.setNewPassword($scope.user)
				.success(function (data) {
					informService.showSimpleToast('Hasło zostało zmienione');
					form.$setPristine();
					form.$setUntouched();
					$scope.close(form);
				})
				.error(function (data, status) {
					switch (data.code) {
						case 'password':
							informService.showAlert('Błąd', 'Podano błędne nowe hasło');
							break;
						default:
							informService.showAlert('Błąd', 'Dane nie zostały zapisane');
					}
					if (status === 401) {
						$rootScope.$emit('session.timeout', '');
					}
				});
			}
		};

		$scope.reset = function(form) {
			if (form) {
				$scope.isEdit = false;
				$scope.title = 'Nowy użytkownik';
				$scope.user = {};
				form.$setPristine();
				form.$setUntouched();
			}
		};

		$scope.close = function(form) {
			$scope.reset(form);
			$scope.closeRight();
		};


	}]);