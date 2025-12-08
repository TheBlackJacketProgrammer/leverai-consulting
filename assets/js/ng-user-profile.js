app.controller("ng-user-profile", ['$scope', '$http', function ($scope, $http) {

    $scope.credentials = {
        fullname: '',
        new_password: '',
        confirm_password: ''
    };

    $scope.init = function() {
        // $("html").addClass("loading");
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/get_user_profile",
        }).then(function successCallback(response) {
            $scope.credentials.fullname = response.data.user_profile.name;
            // $("html").removeClass("loading");
        }).catch(function errorCallback(error) {
            toastr.error(error.data);
            // $("html").removeClass("loading");
        });
    };

    $scope.updateUserProfile = function() {

        if(!$scope.credentials.fullname && !$scope.credentials.new_password && !$scope.credentials.confirm_password) {
            toastr.error('Please fill out all required fields.');
            return;
        }

        if($scope.credentials.new_password && !$scope.credentials.confirm_password) {
            toastr.error('Please confirm the new password.');
            return;
        }

        if($scope.credentials.new_password !== $scope.credentials.confirm_password) {
            toastr.error('New password and confirm password do not match.');
            return;
        }

        $("html").addClass("loading");
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/update_user_profile",
            data: $scope.credentials
        }).then(function successCallback(response) {
            if(response.data.success) {
                toastr.success("User profile updated successfully.");
                $scope.credentials = {
                    fullname: '',
                    new_password: '',
                    confirm_password: ''
                };
                $scope.init();
                $scope.closeModal('modal_user_profile');
                $("html").removeClass("loading");
            } 
            else {
                toastr.error(response.data.error);
                $("html").removeClass("loading");
            }
        }).catch(function errorCallback(error) {
            toastr.error(error.data);
            $("html").removeClass("loading");
        });
    };

}]);