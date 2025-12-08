app.controller("ng-subscribe", ['$scope', '$http', function ($scope, $http) {

    // Test 
    $scope.credentials = {
        fullname: "",
        email: "",
        password: "",
        confirmPassword: "",
        subscriptionPlan: ""
    };

    $scope.init = function() {
        console.log('Subscribe Controller Initialized');
    };


    // Map plans to backend identifiers
    $scope.plans = {
        basic: { id: 'price_12345', label: '1 hour per month - $50.00' },
        standard: { id: 'price_67890', label: '10 hours per month - $400.00' },
        pro: { id: 'price_98765', label: '100 hours per month - $3000.00' },
        daily: { id: 'price_123456', label: 'Daily - $50.00' }
    };

    $scope.proceedForPayment = function() {
        $("html").addClass("loading");
        // console.log('Proceed for Payment called');
        // console.log('Credentials:', $scope.credentials);

        // Simple form validation
        if (!$scope.credentials.fullname || !$scope.credentials.email || !$scope.credentials.password || !$scope.credentials.confirmPassword) {
            toastr.error('Please fill out all required fields.');
            $("html").removeClass("loading");
            return;
        }

        if ($scope.credentials.password !== $scope.credentials.confirmPassword) {
            toastr.error('Passwords do not match.');
            $("html").removeClass("loading");
            return;
        }

        if (!$scope.credentials.subscriptionPlan) {
            toastr.error('Please select a subscription plan.');
            $("html").removeClass("loading");
            return;
        }

        console.log('All validations passed, proceeding with API call');

        // Prepare request payload
        const payload = {
            name: $scope.credentials.fullname,
            email: $scope.credentials.email,
            password: $scope.credentials.password,
            plan: $scope.credentials.subscriptionPlan
        };

        // Send data to backend API
        // console.log('Sending payload:', payload);
        
        // Determine API URL based on environment
        const hostname = window.location.hostname;
        const currentPath = window.location.pathname;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.includes('localhost');
        const hasCi3Template = currentPath.includes('/ci3_template/');
        
        // Use ci3_template path if we're local OR if the current URL contains ci3_template
        const apiUrl = (isLocal || hasCi3Template) ? '/ci3_template/api/register_and_checkout' : '/api/register_and_checkout';
        
        $http.post(apiUrl, payload)
        .then(function(response) {
            console.log('API Response:', response.data);
            if (response.data.url) {
                $("html").removeClass("loading");
                // Redirect to Stripe Checkout
                console.log('Redirecting to Stripe Checkout:', response.data.url);
                window.location.href = response.data.url;
            } else {
                $("html").removeClass("loading");
                toastr.error('Failed to create payment session.');
            }
        }, function(error) {
            // console.error('Full error object:', error);
            // console.error('Error status:', error.status);
            // console.error('Error data:', error.data);
            // console.error('Error config:', error.config);
            toastr.error(error.data.error || 'Failed to create payment session.');
            $("html").removeClass("loading");
        });
    };

    $scope.init();

}]);
