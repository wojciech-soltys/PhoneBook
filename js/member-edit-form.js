function memberEditController($scope) {

}

app.component('memberEditForm', {
	templateUrl: 'include/member-edit-form.html',
	controller: memberEditController,
	bindings: {
		save: '&'
	}
});