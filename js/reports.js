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

	    const LP = 'lp.';
	    const FIRST_NAME = 'firstName';
	    const LAST_NAME = 'lastName';
	    const PHONE = 'phone';
	    const PRIVATE_EMAIL = 'privateEmail';
	    const BIRTHDATE = 'birthDate';
	    const CARDNUMBER = 'cardNumber';
	    const DECLARATION = 'declaration';
	    const ACCESIONDATE = 'accessionDate';

	    function getPolishKey(key) {
	    	switch(key) {
    			case FIRST_NAME :
					return 'Imię';
    			case LAST_NAME :
    				return 'Nazwisko';
    			case PHONE :
    				return 'Nr. telefonu';
    			case PRIVATE_EMAIL :
    				return 'E-mail';
    			case BIRTHDATE :
    				return 'Data urodzenia';
    			case CARDNUMBER :
    				return 'Numer karty członkowskiej';
    			case DECLARATION :
    				return 'Deklaracja';
    			case ACCESIONDATE :
    				return 'Data wstąpienia';
    			default:
        			return key;
			}
	    }

	    function isKeyInReportStructure(key) {
	    	switch(key) {
	    		case LP :
	    			return $scope.reportStructure.lp === '1';
	    		case FIRST_NAME :
	    			return $scope.reportStructure.firstName === '1';
	    		case LAST_NAME : 
	    			return $scope.reportStructure.lastName === '1';
	    		case PHONE :
	    			return $scope.reportStructure.phone === '1';
	    		case PRIVATE_EMAIL :
	    			return $scope.reportStructure.privateEmail === '1';
	    		case BIRTHDATE :
	    			return $scope.reportStructure.birthDate === '1';
	    		case CARDNUMBER :
	    			return $scope.reportStructure.cardNumber === '1';
	    		case DECLARATION :
	    			return $scope.reportStructure.declaration === '1';
	    		case ACCESIONDATE :
	    			return $scope.reportStructure.accessionDate === '1';
	    		default :
	    			return false;
	    	}
	    }

	    function Json2CSVForReports(objArray)
	    {
	      var getKeys = function(obj){
	          var keys = [];
	          if(isKeyInReportStructure(LP)) {
	          	keys.push(LP);
	          }
	          for(var key in obj) {
	          	if(isKeyInReportStructure(key)) {
	            	keys.push(getPolishKey(key));
	        	}
	          }
	          return keys.join();
	        }, array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray
	        , str = ''
	      ;

	      for (var i = 0; i < array.length; i++) {
	        var line = '';
	        if (isKeyInReportStructure(LP)) {
	        	line = i+1;
	        }
	        for (var index in array[i]) {
	          if(isKeyInReportStructure(index)) {
		          if(line != '') line += ','
		      	  line += array[i][index];
	      	  }
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
				Json2CSVForReports(data);
			})
			.error(function (data, status) {
				informService.showSimpleToast('Błąd pobrania danych do raportu');
				if (status === 401) {
					$rootScope.$emit('session.timeout', '');
				}
			});
		};
}]);