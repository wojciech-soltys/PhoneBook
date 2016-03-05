app.controller('paymentNewCtrl',['$scope', '$rootScope', '$stateParams', 'membersService', 'informService',
	function ($scope, $rootScope, $stateParams, membersService,  informService) {
		'use strict';
		$scope.payment = {};
		$scope.payment.memberId = $stateParams.id;

		$scope.paymentTypes = [
			{'id': 1, 'name': 'Semestr 1'},
			{'id': 2, 'name': 'Semestr 2'},
			{'id': 3, 'name': 'Rok'}
		];

		$scope.paymentAmounts = [
			{'id': 20, 'name': '20,00 zł'},
			{'id': 40, 'name': '40,00 zł'}
		];


		$rootScope.$on('clear.edit.user', function (event, value) {
			$scope.reset($scope.userEditForm);
		});

		var setExpirationDate = function() {
			var currentDate = new Date();
			switch ($scope.payment.type) {
				case 1 :
					$scope.payment.expirationDate = new Date((currentDate.getFullYear() + 1) + '-01-31');
					break;
				case 2 : 
					$scope.payment.expirationDate = new Date(currentDate.getFullYear() + '-09-31');
					break
				case 3 : 
					$scope.payment.expirationDate = new Date((currentDate.getFullYear() + 1) + '-09-31');
					break;
			}
		};

		var setAmount = function() {
			switch ($scope.payment.type) {
				case 1 :
				case 2 : 
					$scope.payment.amount = 20;
					break
				case 3 : 
					$scope.payment.amount = 40;
					break;
			}
		};

		$scope.$watch('payment.type', function() {
			setExpirationDate();
			setAmount();
		});

		$scope.closeRight();

		var validation = function(form) {
			if (form.$invalid) {
				informService.showAlert('Błąd', 'Wypełnij poprawnie formularz');
				return false;
			} else {
				return true;
			}
		};

		$scope.save = function(form) {
			if (validation(form)) {
				membersService.setNewPayment($scope.payment)
				.success(function (data) {
					informService.showSimpleToast('Składka członkowska została zapisana');
					form.$setPristine();
					form.$setUntouched();
					$rootScope.$emit('refresh.user.payments', '');
					$scope.close(form);
				})
				.error(function (data, status) {
					informService.showAlert('Błąd', 'Dane nie zostały zapisane');
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