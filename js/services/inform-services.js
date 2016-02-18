angular.module('main.inform', [])
.factory('informService', ['$mdDialog', '$mdToast', function($mdDialog, $mdToast) {
	'use strict';
	var informService={};

	informService.showAlert = function(title, content) {
		$mdDialog.show(
			$mdDialog.alert()
			.parent(angular.element(document.querySelector('#popupContainer')))
			.clickOutsideToClose(true)
			.title(title)
			.textContent(content)
			.ariaLabel('Ok')
			.ok('Ok')
			);
	};

	informService.showSimpleToast = function(content) {
		$mdToast.show(
			$mdToast.simple()
			.textContent(content)
			.position('bottom right')
			.hideDelay(3000)
			);
	};

	informService.showConfirm = function(title, content) {
		return $mdDialog.confirm()
			.title(title)
			.textContent(content)
			.ariaLabel(content)
			.cancel('Anuluj')
			.ok('Tak');
			
	};

	return informService;
}]);