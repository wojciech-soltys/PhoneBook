app.controller('reportsCtrl', ['$scope', '$rootScope', '$stateParams', 'informService', 'membersService', 
	function ($scope, $rootScope, $stateParams, informService, membersService) {

		$scope.userRole = localStorage.getItem('UserRole');

		$scope.reportStructure = {};

		function Json2CSV(objArray)
	    {
	      var getKeys = function(obj){
	          var keys = [];
	          for(var key in obj){
	            keys.push(key);
	          }
	          return keys.join();
	        }, array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray
	        , str = ''
	      ;

	      for (var i = 0; i < array.length; i++) {
	        var line = '';
	        for (var index in array[i]) {
	          if(line != '') line += ','
	       
	          line += array[i][index];
	        }

	        str += line + '\r\n';
	      }

	      str = getKeys(objArray[0]) + '\r\n' + str;

	      var a = document.createElement('a');
	      var blob = new Blob([str], {'type':'application\/octet-stream'});
	      a.href = window.URL.createObjectURL(blob);
	      a.download = 'export.csv';
	      a.click();
	      return true;
	    }	


		$scope.generateReport = function() {		

			membersService.getReportData($scope.reportStructure.onlyWithPaidContribution)
			.success(function (data) {
				Json2CSV(data);
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania danych do raportu');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};
}]);