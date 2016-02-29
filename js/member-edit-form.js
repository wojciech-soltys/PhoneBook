function memberEditController($scope, informService, membersService) {
	$scope.member = null;
	$scope.mentors = null;
	$scope.email = /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
	$scope.types = [
	{id: 'C', name: 'Członek zwyczajny'},
	{id: 'Z', name: 'Zarząd'},
	{id: 'R', name: "Komisja Rewizyjna"},
	{id: 'K', name: 'Koordynator grupy roboczej'},
	{id: 'H', name: 'Członek honorowy'}];

	var validate = function(form) {
		if (form.$invalid) {
			informService.showAlert('Błąd', 'Wypełnij poprawnie formularz');
			return false;
		} else {
			return true;
		}
	};

	$scope.saveMember = function(form) {
		if (validate(form)) {
			membersService.saveMember($scope.member)
			.success(function () {
				informService.showSimpleToast('Zapisano nowego członka');
				$scope.member = null;
				getMentors();
			})
			.error(function () {
				informService.showAlert('Błąd', 'Zapis nie powiódł się.');
			});
		}
	};

	var getMentors = function() {
		membersService.getMentors()
		.success(function (data) {
			$scope.mentors = data;
		})
		.error(function () {
			informService.showSimpleToast('Błąd pobrania listy mentorów');
		});
	};

	var init = function() {
		getMentors();
	};

	init();
};

app.component('memberEditForm', {
	templateUrl: 'include/member-edit-form.html',
	controller: memberEditController,
	bindings: {
		save: '&'
	}
});