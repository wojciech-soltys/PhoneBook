app.controller('userEditCtrl',['$scope', '$rootScope', 'usersService', 'membersService', 'informService',
	function ($scope, $rootScope, usersService, membersService,  informService) {
		'use strict';
		$scope.membersList = null;
		$scope.isEdit = false;
		$scope.changePassword = 0;
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

		$scope.save= function(form) {
			if (validation(form)) {
				usersService.setNewUser($scope.user)
				.success(function (data) {
					$scope.user = data;
					informService.showSimpleToast('Użytkownik został utworzony');
					form.$setPristine();
					form.$setUntouched();
				})
				.error(function (data) {
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
				});
			}
		};


	}]);