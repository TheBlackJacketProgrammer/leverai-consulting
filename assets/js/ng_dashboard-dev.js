app.controller("ng-dashboard-dev", ['$scope', '$http', function ($scope, $http) {
    // Initialize controller
    $scope.init = function() {
        
        // Determine API URL based on environment
        const hostname = window.location.hostname;
        const currentPath = window.location.pathname;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.includes('localhost');
        const hasCi3Template = currentPath.includes('/leverai-consulting/');
          
        // Use leverai-consulting path if we're local OR if the current URL contains leverai-consulting
        const baseUrl = (isLocal || hasCi3Template) ? '/leverai-consulting/' : '/';

        // Set the base URL
        $scope.baseUrl = window.location.protocol + '//' + window.location.host + baseUrl;
        
    }; 

    // Top Up Test
    $scope.topUpTest = function() {
        console.log('Top Up Test called');
    };

    // Test Login
    $scope.testLogin = function() {
        $("html").addClass("loading");
        $http({
            method: "POST",
            url: $scope.baseUrl + "test_login",
            data: $scope.credentials
        }).then(function successCallback(response) {
            $("html").removeClass("loading");
            if(response.data.message == 'Login successful!') {
                toastr.success('Login successful!');
                $scope.remainingHours = response.data.remaining_hours;
            } else {
                toastr.error(response.data.error);
            }
        });
    };

    // Close Modal
    $scope.closeModal = function(modalId) {
        $('#' + modalId).removeClass('flex');
        $('#' + modalId).addClass('hidden');
        
        // Re-enable page scroll when modal is closed
        $('body').removeClass('modal-open');
        
        $scope.credentials = {};
    };

    // Account Test Login
    $scope.openModal = function(modalId) {
        $('#' + modalId).removeClass('hidden');
        $('#' + modalId).addClass('flex');
        
        // Disable page scroll when modal is open
        $('body').addClass('modal-open');
    };

    // Top Up Test - Variables
    $scope.topUpHours = 0;
    $scope.topUpPrice = 100;
    $scope.topUpTotal = 0;
    $scope.remainingHours = 0;

    // Top Up Test - Add/Subtract
    $scope.btnTopUpHours = function(mode) {
        if(mode === 'add') {
            $scope.topUpHours++;
        } else {
            $scope.topUpHours--;
        }
        if($scope.topUpHours < 0) {
            $scope.topUpHours = 0;
        }
        $scope.topUpTotal = $scope.topUpHours * $scope.topUpPrice;
    };

    // Top Up Test - Proceed to Payment
    $scope.btnTopUpProceed = function() {
        $("html").addClass("loading");
        console.log('Proceed to Payment called');

        $http({
            method: "POST",
            url: $scope.baseUrl + "api/top_up",
            data: {
                hours: $scope.topUpHours,
                total: $scope.topUpTotal
            }
        }).then(function successCallback(response) {
            console.log('API Response:', response.data);
            $("html").removeClass("loading");
            
            if (response.data.success && response.data.checkout_url) {
                // Redirect to Stripe checkout
                $scope.remainingHours = parseInt($scope.remainingHours) + parseInt($scope.topUpHours);
                window.location.href = response.data.checkout_url;
            } else {
                toastr.error(response.data.error || 'Failed to process top-up.');
            }
            
        }, function errorCallback(error) {
            console.error('Full error object:', error);
            console.error('Error status:', error.status);
            console.error('Error data:', error.data);
            console.error('Error config:', error.config);
            $("html").removeClass("loading");
            
            // Show user-friendly error message
            if (error.data && error.data.error) {
                toastr.error(error.data.error);
            } else {
                toastr.error('An error occurred while processing your payment. Please try again.');
            }
        });

    };

    // Test Logout - Clear Session
    $scope.testLogout = function() {
        $http({
            method: "POST",
            url: $scope.baseUrl + "test_logout",
        }).then(function successCallback(response) {
            toastr.success('Session cleared successfully');
        }, function errorCallback(error) {
            toastr.error('Failed to clear session');
        });
    };
    
    



    
    // Initialize the controller
    $scope.init();
}]);