function paymentTableController($scope, $rootScope, $stateParams, $state, informService, membersService) {
	$scope.paymentsList = {};
	$scope.memberId = $stateParams.id;

	var getPaymentsForMember = function() {
		membersService.getPaymentsForMember($scope.memberId)
		.success(function (data) {
			$scope.paymentsList = data;
		})
		.error(function () {
			informService.showSimpleToast('Błąd pobrania historii składek');
		});
	};
	getPaymentsForMember();
	$rootScope.$on('refresh.payments.table', function () {
		getPaymentsForMember();	
	});
};

app.component('paymentsTable', {
	templateUrl: 'include/payments-table.html',
	controller: paymentTableController
});