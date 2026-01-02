app.controller("ng-login", ['$scope', '$http', function ($scope, $http) {

    $scope.credentials = {};

    $scope.login = function() {

        if(!$scope.credentials.email || !$scope.credentials.password) {
            toastr.error('Please fill out all required fields.');
            return;
        }

        $("html").addClass("loading");
        $http({
            method: "POST",
            url: $scope.baseUrl + "authenticate",
            data: $scope.credentials
        }).then(function successCallback(response) {
            if(response.data.success) {
                toastr.success(response.data.message);
                window.location.href = $scope.baseUrl;
            } 
            else {
                toastr.error(response.data.message);
                if(response.data.requires_payment) {
                    window.location.href = response.data.checkout_url;
                }
                $("html").removeClass("loading");
            }
        }).catch(function errorCallback(error) {
            toastr.error(error.data);
            $("html").removeClass("loading");
        });
    };

    $scope.openModal = function(modalId) {
        console.log('Open Modal called');
        $('#' + modalId).removeClass('hidden');
        $('#' + modalId).addClass('flex');
        
        // Disable page scroll when modal is open
        $('body').addClass('modal-open');
    };

    $scope.resetPassword = function() {
        console.log('Retrieve Password called');
        console.log('Credentials:', $scope.credentials);

        if(!$scope.credentials.email || !$scope.credentials.secret_question || !$scope.credentials.secret_answer || !$scope.credentials.new_password) {
            toastr.error('Please fill out all required fields.');
            return;
        }

        $("html").addClass("loading");
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/reset_password",
            data: $scope.credentials
        }).then(function successCallback(response) {
            if(response.data.success) {
                toastr.success(response.data.message);
                window.location.href = $scope.baseUrl + "login";
            }
            else {
                toastr.error(response.data.message);
            }
            $("html").removeClass("loading");
        });
    };
}]);