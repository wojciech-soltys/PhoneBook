if(!MyApp)
{
	var MyApp=angular.module('logout', []);
}

MyApp.controller('logout', function ($scope, $http, $window) {
		function init() {
		var request = $http({
			method: 'post',
			url: 'php/logout.php',
			data: {
				username: localStorage.getItem('Username'),
				session_id: localStorage.getItem('SessionID')
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		});

		request.success(function (data) {
			if (data != '401') {
				localStorage.removeItem('Username');
				localStorage.removeItem('TimeStamp');
				localStorage.removeItem('SessionID');
				localStorage.removeItem('UserRole');
				
				$window.location.href='login.html';
				
			}
		});
		}
		
		init();
	});