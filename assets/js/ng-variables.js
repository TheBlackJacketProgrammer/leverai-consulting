// Controller Script For Global Variables
// This controller manages global variables and state for the application

app.controller("ng-variables", ['$scope', function($scope) {
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

        $scope.openModal = function(modalId) {
          console.log('Open Modal called');
          $('#' + modalId).removeClass('hidden');
          $('#' + modalId).addClass('flex');
          
          // Disable page scroll when modal is open
          $('body').addClass('modal-open');
        };
        
        // Initialize the controller
        $scope.init();
    }]
); 