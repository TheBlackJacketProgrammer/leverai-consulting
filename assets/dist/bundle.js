// Main AngularJS application module
// var app = angular.module('leverai-dev', ['datatables']); 
var app = angular.module('leverai-dev', []); 

// Custom directive for touch events
app.directive('ngTouchstart', function() {
    return function(scope, element, attrs) {
        element.bind('touchstart', function(event) {
            scope.$apply(function() {
                scope.$eval(attrs.ngTouchstart, {$event: event});
            });
        });
    };
});

app.directive('ngTouchmove', function() {
    return function(scope, element, attrs) {
        element.bind('touchmove', function(event) {
            scope.$apply(function() {
                scope.$eval(attrs.ngTouchmove, {$event: event});
            });
        });
    };
});

// For Partial View Use
app.directive('compile', ['$compile', function ($compile) 
{
    return function(scope, element, attrs) 
    {
        scope.$watch(function(scope) 
        {
            return scope.$eval(attrs.compile);
        },function(value)
        {
            element.html(value);
            $compile(element.contents())(scope);
        });
    };
}]);

    app.directive('fileModel', ['$parse', function ($parse) 
    {
      return {
        restrict: 'A',
        link: function(scope, element, attrs) 
              {
                var model = $parse(attrs.fileModel);
                var modelSetter = model.assign;
        
                element.bind('change', function() 
                {
                  scope.$apply(function() 
                  {
                    modelSetter(scope, element[0].files[0]);
                  });
                });
              }
        };
    }]);
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
app.controller("ng-header", ['$scope', '$http', '$document', '$interval', function ($scope, $http, $document, $interval) {

    // Initialize notification panel state
    $scope.notificationPanelOpen = false;
    $scope.pollingInterval = null;
    $scope.pollingIntervalMs = 30000; // Poll every 30 seconds (30000ms)
    $scope.hasMoreNotifications = false;

    $scope.init = function() {
        console.log('Header with notifications detected!');
        $scope.notificationPanelOpen = false;
        // Initial load with limit of 5 notifications
        $scope.getNotifications(null, 5);
        
        // Start polling for notifications
        $scope.startPolling();
        
        // Handle clicks outside notification dropdown
        $document.on('click', function(event) {
            var notificationDropdown = angular.element(document.querySelector('.notification-dropdown'));
            
            // Check if click is outside the notification dropdown
            if ($scope.notificationPanelOpen && 
                notificationDropdown.length > 0 &&
                !notificationDropdown[0].contains(event.target)) {
                $scope.$apply(function() {
                    $scope.notificationPanelOpen = false;
                });
            }
        });
    };

    // Helper function to format notification date
    $scope.formatNotificationDate = function(dateStr) {
        if (!dateStr) return '';
        
        // Remove microseconds if present (format: 2025-10-29 14:28:41.571694)
        var cleanDateStr = dateStr.split('.')[0];
        
        // Replace space with 'T' to create ISO-like format for Date constructor
        // Replace ' ' with 'T' and add 'Z' for UTC, or parse directly
        var date = new Date(cleanDateStr.replace(' ', 'T'));
        
        // Check if date is valid
        if (isNaN(date.getTime())) return '';
        
        // Format as readable string: "Oct 29, 2025 2:28 PM"
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    };

    $scope.getNotifications = function(since, limit) {
        var url = $scope.baseUrl + "api/get_notifications";
        var params = [];
        if (since) {
            params.push("since=" + encodeURIComponent(since));
        }
        if (limit) {
            params.push("limit=" + limit);
        }
        if (params.length > 0) {
            url += "?" + params.join("&");
        }
        
        $http({
            method: "GET",
            url: url
        }).then(function successCallback(response) {
            var notifications = response.data.notifications;
            var isPolling = !!since;
            var isInitialLoad = !isPolling;
            
            console.log('Notifications response - since:', since, 'count:', notifications ? notifications.length : 0);
            
            // Handle invalid response
            if (!Array.isArray(notifications)) {
                if (isInitialLoad) {
                    $scope.notifications = [];
                    $scope.notificationCount = 0;
                }
                return;
            }
            
            // Create Set of existing notification IDs for O(1) lookup
            var existingIds = new Set();
            if ($scope.notifications) {
                $scope.notifications.forEach(function(n) {
                    existingIds.add(n.id);
                });
            }
            
            // Process notifications
            var newNotifications = [];
            notifications.forEach(function(notification) {
                // Skip duplicates during polling
                if (isPolling && existingIds.has(notification.id)) {
                    return;
                }
                
                // Format date if present
                if (notification.created_at) {
                    notification.formatted_date = $scope.formatNotificationDate(notification.created_at);
                }
                
                newNotifications.push(notification);
            });
            
            // Update notifications list
            if (isPolling && newNotifications.length > 0) {
                // Prepend new notifications to existing list
                $scope.notifications = newNotifications.concat($scope.notifications || []);
            } else if (isInitialLoad) {
                // Initial load - replace all notifications
                $scope.notifications = notifications.map(function(n) {
                    if (n.created_at) {
                        n.formatted_date = $scope.formatNotificationDate(n.created_at);
                    }
                    return n;
                });
            }
            
            // Update count (only unread notifications where is_read = 'f')
            $scope.notificationCount = $scope.notifications ? $scope.notifications.filter(function(n) { return n.is_read === 'f'; }).length : 0;
            
            // Check if there are more notifications to load
            // If we got 5 or more notifications, there might be more
            if (isInitialLoad) {
                $scope.checkForMoreNotifications();
            } else if (isPolling) {
                // After polling, check again
                $scope.checkForMoreNotifications();
            }
        }).catch(function errorCallback(error) {
            console.error('Error getting notifications:', error);
            if (!since) {
                $scope.notificationCount = 0;
            }
        });
    };
    
    // Check if there are more notifications to load
    $scope.checkForMoreNotifications = function() {
        if (!$scope.notifications || $scope.notifications.length === 0) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        // Get the oldest notification's date
        var oldestNotification = $scope.notifications[$scope.notifications.length - 1];
        if (!oldestNotification || !oldestNotification.created_at) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        // Check if there are more notifications before the oldest one
        var before = $scope.cleanDateForSince(oldestNotification.created_at);
        var url = $scope.baseUrl + "api/get_notifications?before=" + encodeURIComponent(before) + "&limit=1";
        
        $http({
            method: "GET",
            url: url
        }).then(function successCallback(response) {
            var notifications = response.data.notifications;
            $scope.hasMoreNotifications = Array.isArray(notifications) && notifications.length > 0;
        }).catch(function errorCallback(error) {
            console.error('Error checking for more notifications:', error);
            $scope.hasMoreNotifications = false;
        });
    };
    
    // Load previous (older) notifications
    $scope.loadPreviousNotifications = function(event) {
        if (event) {
            event.stopPropagation();
        }
        
        if (!$scope.notifications || $scope.notifications.length === 0) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        // Get the oldest notification's date
        var oldestNotification = $scope.notifications[$scope.notifications.length - 1];
        if (!oldestNotification || !oldestNotification.created_at) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        var before = $scope.cleanDateForSince(oldestNotification.created_at);
        var url = $scope.baseUrl + "api/get_notifications?before=" + encodeURIComponent(before) + "&limit=5";
        
        $http({
            method: "GET",
            url: url
        }).then(function successCallback(response) {
            var notifications = response.data.notifications;
            
            if (!Array.isArray(notifications) || notifications.length === 0) {
                $scope.hasMoreNotifications = false;
                return;
            }
            
            // Format dates for new notifications
            notifications.forEach(function(notification) {
                if (notification.created_at) {
                    notification.formatted_date = $scope.formatNotificationDate(notification.created_at);
                }
            });
            
            // Append to existing notifications
            $scope.notifications = $scope.notifications.concat(notifications);
            
            // Check if there are more notifications to load
            $scope.checkForMoreNotifications();
        }).catch(function errorCallback(error) {
            console.error('Error loading previous notifications:', error);
            $scope.hasMoreNotifications = false;
        });
    };

    // Helper function to clean date string for 'since' parameter (remove microseconds)
    $scope.cleanDateForSince = function(dateStr) {
        return dateStr ? dateStr.split('.')[0] : null;
    };

    // Start polling for new notifications
    $scope.startPolling = function() {
        // Stop any existing polling
        $scope.stopPolling();
        
        // Start polling interval
        $scope.pollingInterval = $interval(function() {
            var since = null;
            var notifications = $scope.notifications;
            var count = notifications ? notifications.length : 0;
            
            if (count > 0) {
                // Use the most recent notification's created_at timestamp
                since = $scope.cleanDateForSince(notifications[0].created_at);
                console.log('Polling with since:', since, 'Current notifications count:', count);
            } else {
                console.log('Polling without since (full refresh), current notifications count:', count);
            }
            
            $scope.getNotifications(since);
        }, $scope.pollingIntervalMs);
        
        console.log('Notification polling started (interval: ' + $scope.pollingIntervalMs + 'ms)');
    };

    // Stop polling for notifications
    $scope.stopPolling = function() {
        if ($scope.pollingInterval) {
            $interval.cancel($scope.pollingInterval);
            $scope.pollingInterval = null;
            console.log('Notification polling stopped');
        }
    };

    $scope.openModal = function(modalId) {
        console.log('Open Modal called');
        $('#' + modalId).removeClass('hidden');
        $('#' + modalId).addClass('flex');
        
        // Disable page scroll when modal is open
        $('body').addClass('modal-open');
    };

    // Toggle notification panel
    $scope.toggleNotificationPanel = function(event) {
        $scope.getAllTickets();
        if (event) {
            event.stopPropagation();
        }
        $scope.notificationPanelOpen = !$scope.notificationPanelOpen;
    };

    $scope.markNotificationAsRead = function(notificationId, event) {
        event.stopPropagation();
        // console.log('Mark notification as read:', notificationId);
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/mark_as_read",
            data: {
                id: notificationId
            }
        }).then(function successCallback(response) {
            // console.log('Notification marked as read:', response.data);
            // Refresh notifications to update the list and count (keep same limit as initial load)
            $scope.getNotifications(null, 5);
        }).catch(function errorCallback(error) {
            console.error('Error marking notification as read:', error);
        });
    };

    $scope.markAllAsRead = function(event) {
        event.stopPropagation();
        //console.log('Mark all notifications as read');
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/mark_all_as_read",
            data: {
            }
        }).then(function successCallback(response) {
            // Refresh notifications to update the list and count (keep same limit as initial load)
            $scope.getNotifications(null, 5);
        }).catch(function errorCallback(error) {
            console.error('Error marking all notifications as read:', error);
        });
    };

    // Clean up polling when controller is destroyed
    $scope.$on('$destroy', function() {
        $scope.stopPolling();
    });

    $scope.init();

}]);
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
app.controller("ng-subscribe", ['$scope', '$http', function ($scope, $http) {

    // Test 
    $scope.credentials = {
        fullname: "",
        email: "",
        password: "",
        confirmPassword: "",
        subscriptionPlan: "",
        secret_question: "",
        secret_answer: ""
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

        if (!$scope.credentials.secret_question || !$scope.credentials.secret_answer) {
            toastr.error('Please select a secret question and answer.');
            $("html").removeClass("loading");
            return;
        }

        console.log('All validations passed, proceeding with API call');

        // Prepare request payload
        const payload = {
            name: $scope.credentials.fullname,
            email: $scope.credentials.email,
            password: $scope.credentials.password,
            plan: $scope.credentials.subscriptionPlan,
            secret_question: $scope.credentials.secret_question,
            secret_answer: $scope.credentials.secret_answer
        };

        // Send data to backend API
        // console.log('Sending payload:', payload);
        
        // Determine API URL based on environment
        const hostname = window.location.hostname;
        const currentPath = window.location.pathname;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname.includes('localhost');
        const hasCi3Template = currentPath.includes('/leverai-consulting/');
        
        // Use leverai-consulting path if we're local OR if the current URL contains leverai-consulting
        const apiUrl = (isLocal || hasCi3Template) ? '/leverai-consulting/api/register_and_checkout' : '/api/register_and_checkout';
        
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
    $scope.topUpPrice = 50;
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
app.controller("ng-dashboard-customer", ['$scope', '$http', function ($scope, $http) {
    $scope.topUpHours = 0;
    $scope.topUpTotal = 0;
    $scope.remainingHours = 0;
    $scope.topUpPrice = 50;
    
    // Pagination
    $scope.currentPage = 1;
    $scope.itemsPerPage = 5;

    // Search
    $scope.searchText = '';
    $scope.filteredData = [];
    $scope.loadingTickets = true;
    $scope.ticketDetails = {};
    $scope.ticketComments = [];
    $scope.commentData = { text: '' };
    $scope.sendingComment = false;

    $scope.dedicate_hours_temp = 0;
    $scope.tid_temp = null;
    
    // Watch for search text changes and reset pagination
    $scope.$watch('searchText', function(newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.currentPage = 1;
            $scope.applyFilter();
        }
    });
   
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

        // Get all tickets
        $scope.getAllTickets();
        
        // Get remaining hours
        $scope.getRemainingHours();

    }; 

    // Close Modal
    $scope.closeModal = function(modalId) {
        $('#' + modalId).removeClass('flex');
        $('#' + modalId).addClass('hidden');
        
        // Re-enable page scroll when modal is closed
        $('body').removeClass('modal-open');
        
        $scope.topUpHours = 0;
        $scope.topUpTotal = 0;
        $scope.request = {};
        $scope.tid_temp = null;
        $scope.dedicate_hours_temp = 0;
        $scope.ticketDetails = {};
        $scope.ticketComments = [];
    };

    // Account Test Login
    $scope.openModal = function(modalId) {
        $('#' + modalId).removeClass('hidden');
        $('#' + modalId).addClass('flex');

        console.log("Modal ID:", modalId);
        
        // Disable page scroll when modal is open
        $('body').addClass('modal-open');
    };

    // View Request
    $scope.openRequestModal = function(modalId, ticket_id) {
        $('html').addClass('loading');
        // console.log("Modal ID:", modalId);
        // console.log("Data:", data);
        $scope.tid_temp = ticket_id;
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/get_ticket_with_comments",
            data: { ticket_id: $scope.tid_temp }
        }).then(function successCallback(response) {
            $scope.ticketDetails = response.data.ticket_details;
            $scope.ticketComments = response.data.ticket_comments;
            $scope.dedicate_hours_temp = Number($scope.ticketDetails.dedicate_hours);
            $scope.commentData.text = '';
            $scope.sendingComment = false;
            // console.log($scope.ticketDetails);
            // console.log($scope.ticketComments);
            $('#' + modalId).removeClass('hidden');
            $('#' + modalId).addClass('flex');
            
            // Disable page scroll when modal is open
            $('body').addClass('modal-open');
            
            // Scroll to the latest comment after modal is opened and comments are loaded
            setTimeout(function() {
                var commentsContainer = document.querySelector('.comments-container');
                if (commentsContainer) {
                    commentsContainer.scrollTop = commentsContainer.scrollHeight;
                }
            }, 100);
        }).finally(function() {
            $('html').removeClass('loading');
        });
    };

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
                total: $scope.topUpTotal,
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

    // Get remaining hours
    $scope.getRemainingHours = function() {
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/remaining_hours",
        }).then(function successCallback(response) {
            $scope.hours_remaining = response.data.hours_remaining;
            console.log('Hours remaining:', $scope.hours_remaining);
        }, function errorCallback(error) {
            console.error('Error fetching remaining hours:', error);
        });
    };

    // Filter function to apply search
    $scope.applyFilter = function() {
        if ($scope.searchText && $scope.searchText.trim() !== '') {
            $scope.filteredData = $scope.tickets.filter(function(item) {
                return (
                    (item.ticket_id && item.ticket_id.toString().includes($scope.searchText)) ||
                    (item.title && item.title.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.dedicate_hours && item.dedicate_hours.toString().includes($scope.searchText)) ||
                    (item.status && item.status.toLowerCase().includes($scope.searchText.toLowerCase()))
                );
            });
        } else {
            $scope.filteredData = $scope.tickets;
        }
    };

    // Get all tickets
    $scope.getAllTickets = function() {
        $scope.loadingTickets = true;
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_all_tickets_by_user",
        }).then(function successCallback(response) {
            $scope.tickets = response.data.tickets;
            $scope.applyFilter();
        }).finally(function() {
            $scope.loadingTickets = false;
        });
    };

    // Calculate total pages
    $scope.pageCount = function() {
        if (!$scope.tickets) return 0;
        // Use filteredData if search is active, otherwise use tickets
        const dataToUse = $scope.searchText && $scope.searchText.trim() !== '' ? $scope.filteredData : $scope.tickets;
        if (!dataToUse) return 0;
        return Math.ceil(dataToUse.length / $scope.itemsPerPage);
    };

    // Go to page
    $scope.setPage = function(page) {
        $scope.currentPage = page;
    };

    // Previous page
    $scope.prevPage = function() {
        if ($scope.currentPage > 1) {
          $scope.currentPage--;
        }
    };

    // Next page
    $scope.nextPage = function() {
        if ($scope.currentPage < $scope.pageCount()) {
          $scope.currentPage++;
        }
    };

    // Get pages for pagination display with ellipsis
    $scope.getPages = function() {
        let pages = [];
        const totalPages = $scope.pageCount();
        const currentPage = $scope.currentPage;
        
        if (totalPages <= 4) {
            // Show all pages if 4 or fewer
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            let startPage, endPage;
            
            // Near the end: show last 3 pages
            if (currentPage >= totalPages - 1) {
                startPage = Math.max(1, totalPages - 2);
                endPage = totalPages;
            }
            // Near the beginning: show first 3 pages
            else if (currentPage <= 2) {
                startPage = 1;
                endPage = 3;
            }
            // Pages 3-4: center around current (current-1, current, current+1)
            else if (currentPage <= 4) {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            // Page 5+: show current-1, current, current+1
            else {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            
            // Add the 3 pages
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                pages.push('...');
                pages.push(totalPages);
            }
        }
        
        return pages;
    };

    // Create Request
    $scope.btnCreateRequest = function() {
        $("html").addClass("loading");


        if(!$scope.request || !$scope.request.title || !$scope.request.request_priority || !$scope.request.dedicate_hours || !$scope.request.details) {
            toastr.error('Please fill out all required fields.');
            $("html").removeClass("loading");
            return;
        }

        if($scope.hours_remaining < $scope.request.dedicate_hours || $scope.request.dedicate_hours <= 0) {
            toastr.error('You do not have enough hours to dedicate. Need to top up more hours.');
            $("html").removeClass("loading");
            return;
        }

        $http.post($scope.baseUrl + "api/create_request", $scope.request)
            .then(function(response) {
                if (response.data.success) {
                    // toastr.success('Request created successfully');
                    $scope.closeModal('modal_create_request');
                    $scope.request = {};
                    
                    // Refresh data and remove loading when complete
                    $scope.loadingTickets = true;
                    Promise.all([
                        $http.get($scope.baseUrl + "api/remaining_hours"),
                        $http.get($scope.baseUrl + "api/get_all_tickets_by_user")
                    ]).then(function([hoursRes, ticketsRes]) {
                        $scope.hours_remaining = hoursRes.data.hours_remaining;
                        $scope.tickets = ticketsRes.data.tickets;
                        $scope.applyFilter();
                        $scope.loadingTickets = false;

                        // Trigger AngularJS digest cycle to update the UI
                        $scope.$apply();
                    }).finally(() => {
                        $("html").removeClass("loading");
                        toastr.success('Request created successfully');
                    });
                } else {
                    toastr.error(response.data.error || 'Failed to create request.');
                    $("html").removeClass("loading");
                }
            })
            .catch(function(error) {
                toastr.error('An error occurred while creating request. Please try again.');
                $("html").removeClass("loading");
            });
    };

    // Get all comments
    $scope.getAllComments = function() {
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/get_all_comments",
            data: { ticket_id: $scope.ticketDetails.tid }
        }).then(function successCallback(response) {
            $scope.ticketComments = response.data.comments;
            
            // Scroll to the latest comment after comments are loaded
            setTimeout(function() {
                var commentsContainer = document.querySelector('.comments-container');
                if (commentsContainer) {
                    commentsContainer.scrollTop = commentsContainer.scrollHeight;
                }
            }, 100);
        }).catch(function errorCallback(error) {
            $scope.ticketComments.push({ error: true, message: error.data.error });
        }).finally(function() {
            $scope.sendingComment = false;

        });
    };

    // Send Comment
    $scope.sendComment = function(ticketDetails) {
        console.log('Sending comment for ticket:', ticketDetails);
        console.log('Comment:', $scope.commentData.text);
        // Prevent sending empty comments
        if (!$scope.commentData.text || $scope.commentData.text.trim() === '') {
            toastr.warning('Please enter a comment before sending.');
            return;
        }
        
        // Prevent multiple submissions
        if ($scope.sendingComment) {
            return;
        }
        
        // $('html').addClass('loading');
        $scope.sendingComment = true;
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/send_comment",
            data: { ticket_id: ticketDetails.tid, message: $scope.commentData.text, title: ticketDetails.title, user_id: ticketDetails.uid }
        }).then(function successCallback(response) {
            toastr.success(response.data.message);
            $scope.commentData.text = '';
        }).catch(function errorCallback(error) {
            // toastr.error('Error updating status: ' + error.data.error);
            $scope.ticketComments.push({ error: true, message: error.data.error });
        }).finally(function() {
            // $('html').removeClass('loading');
            $scope.getAllComments();
            $scope.sendingComment = false;
        });
    };

    // Add/Subtract Dedication Hours
    $scope.btnDedicateHours = function(mode) {
        console.log('Dedication hours temp:', $scope.dedicate_hours_temp);
        if(mode === 'add') {
            $scope.hours_remaining--;
            if($scope.hours_remaining < 0) {
                $scope.hours_remaining = 0;
                toastr.error('You do not have enough hours to dedicate. Need to top up more hours.');
                return;
            }
            $scope.ticketDetails.dedicate_hours++;
        }
        else {
            if($scope.ticketDetails.dedicate_hours <= $scope.dedicate_hours_temp){
                $scope.ticketDetails.dedicate_hours = $scope.dedicate_hours_temp;
                toastr.error('You cannot subtract more hours than you have dedicated.');
                return;
            }
            $scope.ticketDetails.dedicate_hours--;
            $scope.hours_remaining++;
        }
    };

    // Update Dedication Hours
    $scope.btnUpdateDedicateHours = function() {
        $('html').addClass('loading');
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/update_dedicate_hours",
            data: { 
                ticket_id: $scope.tid_temp, 
                dedicate_hours: $scope.ticketDetails.dedicate_hours, 
                hours_remaining: $scope.hours_remaining 
            }
        }).then(function successCallback(response) {
            toastr.success(response.data.message);
        }).catch(function errorCallback(error) {
            toastr.error('Error updating status: ' + error.data.error);
        }).finally(function() {
            $('html').removeClass('loading');
            $scope.getAllTickets();
            // Filter is already applied in getAllTickets
        });
    };


    // Initialize the controller
    $scope.init();
}]);
app.controller("ng-dashboard-admin", ['$scope', '$http', '$compile', function ($scope, $http, $compile) {
    // Pagination
    $scope.currentPage = 1;
    $scope.itemsPerPage = 5;

    // Search
    $scope.searchText = '';
    $scope.filteredData = [];
    $scope.loadingTickets = true;
    $scope.sendingComment = false;
    $scope.commentData = { text: '' };
    $scope.billingTotals = [];
    $scope.activePlanCounts = [];
    $scope.ticketCountsByStatus = [];
    
    // Watch for search text changes and reset pagination
    $scope.$watch('searchText', function(newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.currentPage = 1;
            $scope.applyFilter();
        }
    });

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

        // Get billing totals for previous and current month
        $scope.getBillingTotalsPrevCurr();
        // Get active plan counts
        $scope.getActivePlanCounts();
        // Get ticket counts by status
        $scope.getTicketCountsByStatus();

    }; 

    // Filter function to apply search
    $scope.applyFilter = function() {
        if ($scope.searchText && $scope.searchText.trim() !== '') {
            $scope.filteredData = $scope.tickets.filter(function(item) {
                return (
                    (item.ticket_id && item.ticket_id.toString().includes($scope.searchText)) ||
                    (item.title && item.title.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.dedicate_hours && item.dedicate_hours.toString().includes($scope.searchText)) ||
                    (item.status && item.status.toLowerCase().includes($scope.searchText.toLowerCase()))
                );
            });
        } else {
            $scope.filteredData = $scope.tickets;
        }
    };

    // Get all requests
    $scope.getAllRequests = function() {
        $scope.loadingTickets = true;
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_all_tickets",
        }).then(function successCallback(response) {
            $scope.tickets = response.data.tickets;
            $scope.applyFilter();
        }).finally(function() {
            $scope.loadingTickets = false;
        });
    };

    // Calculate total pages
    $scope.pageCount = function() {
        if (!$scope.tickets) return 0;
        // Use filteredData if search is active, otherwise use tickets
        const dataToUse = $scope.searchText && $scope.searchText.trim() !== '' ? $scope.filteredData : $scope.tickets;
        if (!dataToUse) return 0;
        return Math.ceil(dataToUse.length / $scope.itemsPerPage);
    };

    // Go to page
    $scope.setPage = function(page) {
        $scope.currentPage = page;
    };

    // Previous page
    $scope.prevPage = function() {
        if ($scope.currentPage > 1) {
          $scope.currentPage--;
        }
    };

    // Next page
    $scope.nextPage = function() {
        if ($scope.currentPage < $scope.pageCount()) {
          $scope.currentPage++;
        }
    };

    // Get pages for pagination display with ellipsis
    $scope.getPages = function() {
        let pages = [];
        const totalPages = $scope.pageCount();
        const currentPage = $scope.currentPage;
        
        if (totalPages <= 4) {
            // Show all pages if 4 or fewer
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            let startPage, endPage;
            
            // Near the end: show last 3 pages
            if (currentPage >= totalPages - 1) {
                startPage = Math.max(1, totalPages - 2);
                endPage = totalPages;
            }
            // Near the beginning: show first 3 pages
            else if (currentPage <= 2) {
                startPage = 1;
                endPage = 3;
            }
            // Pages 3-4: center around current (current-1, current, current+1)
            else if (currentPage <= 4) {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            // Page 5+: show current-1, current, current+1
            else {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            
            // Add the 3 pages
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                pages.push('...');
                pages.push(totalPages);
            }
        }
        
        return pages;
    };

    // Open Module
    $scope.openModule = function(module) {
        // console.log('Opening Module: ' + module);
        $scope.section = "";
        $("html").addClass("loading");
        $http({
            method: "POST",
            url: $scope.baseUrl + "load_module",
            data: { module: module }
        }).then(function successCallback(response) {
            $("html").removeClass("loading");
            $scope.section = response.data.section;
        }, function errorCallback(error) {
            toastr.error('Error loading module: ' + error.data.error);
            $("html").removeClass("loading");
        });
    };

    // Open Modal
    $scope.openModal = function(modalId, ticket_id) {
        $('html').addClass('loading');
        console.log('Opening Modal: ' + modalId + ' for ticket ID: ' + ticket_id);
        $scope.tid_temp = ticket_id;
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/get_ticket_with_comments",
            data: { ticket_id: $scope.tid_temp }
        }).then(function successCallback(response) {
            $scope.ticketDetails = response.data.ticket_details;
            $scope.ticketComments = response.data.ticket_comments;
            $scope.commentData.text = '';
            $scope.sendingComment = false;
            console.log($scope.ticketDetails);
            console.log($scope.ticketComments);
            $('#' + modalId).removeClass('hidden');
            $('#' + modalId).addClass('flex');
            
            // Disable page scroll when modal is open
            $('body').addClass('modal-open');
            
            // Scroll to the latest comment after modal is opened and comments are loaded
            setTimeout(function() {
                var commentsContainer = document.querySelector('.comments-container');
                if (commentsContainer) {
                    commentsContainer.scrollTop = commentsContainer.scrollHeight;
                }
            }, 100);
        }).finally(function() {
            $('html').removeClass('loading');
        });
    };

    // Close Modal
    $scope.closeModal = function(modalId) {
        $('#' + modalId).removeClass('flex');
        $('#' + modalId).addClass('hidden');
        
        // Re-enable page scroll when modal is closed
        $('body').removeClass('modal-open');
    };

    // Update Status
    $scope.updateStatus = function(ticketDetails) {
        $('html').addClass('loading');
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/update_status",
            data: { ticket_id: ticketDetails.tid, status: ticketDetails.status, dedicate_hours: ticketDetails.dedicate_hours, user_id: ticketDetails.uid }
        }).then(function successCallback(response) {
            if(ticketDetails.status == 'Rejected') {
                toastr.success('Ticket has been rejected and closed.');
                $scope.closeModal('modal_view_request');
                $scope.getAllRequests();
                return;
            }
            toastr.success(response.data.message);
        }).catch(function errorCallback(error) {
            toastr.error('Error updating status: ' + error.data.error);
        }).finally(function() {
            $('html').removeClass('loading');
            $scope.getAllRequests();
        });
    };

    // Get all comments
    $scope.getAllComments = function() {
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/get_all_comments",
            data: { ticket_id: $scope.ticketDetails.tid }
        }).then(function successCallback(response) {
            $scope.ticketComments = response.data.comments;
            
            // Scroll to the latest comment after comments are loaded
            setTimeout(function() {
                var commentsContainer = document.querySelector('.comments-container');
                if (commentsContainer) {
                    commentsContainer.scrollTop = commentsContainer.scrollHeight;
                }
            }, 100);
        }).catch(function errorCallback(error) {
            $scope.ticketComments.push({ error: true, message: error.data.error });
        }).finally(function() {
            $scope.sendingComment = false;

        });
    };

    // Send Comment
    $scope.sendComment = function(ticketDetails) {
        // Prevent sending empty comments
        if (!$scope.commentData.text || $scope.commentData.text.trim() === '') {
            toastr.warning('Please enter a comment before sending.');
            return;
        }
        
        // Prevent multiple submissions
        if ($scope.sendingComment) {
            return;
        }
        
        // $('html').addClass('loading');
        $scope.sendingComment = true;
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/send_comment",
            data: { ticket_id: ticketDetails.tid, message: $scope.commentData.text, title: ticketDetails.title, user_id: ticketDetails.uid }
        }).then(function successCallback(response) {
            toastr.success(response.data.message);
            $scope.commentData.text = '';
        }).catch(function errorCallback(error) {
            // toastr.error('Error updating status: ' + error.data.error);
            $scope.ticketComments.push({ error: true, message: error.data.error });
        }).finally(function() {
            // $('html').removeClass('loading');
            $scope.getAllComments();
            $scope.sendingComment = false;
        });
    };

    // Format currency with proper formatting (commas, decimals)
    $scope.formatCurrency = function(amount) {
        if (amount == null || amount === undefined || isNaN(amount)) {
            return '0.00';
        }
        return parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Calculate total revenue from billing totals
    $scope.getTotalRevenue = function() {
        var total = 0;
        if ($scope.billingTotals && $scope.billingTotals.length > 0) {
            $scope.billingTotals.forEach(function(item) {
                if (item && item.total_amount) {
                    total += parseFloat(item.total_amount) || 0;
                }
            });
        }
        return total;
    }

    // Get billing totals for previous and current month
    $scope.getBillingTotalsPrevCurr = function() {
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_billing_totals_prev_curr",
        }).then(function successCallback(response) {
            $scope.billingTotals = response.data.billing_totals || [];
            console.log('Billing totals loaded:', $scope.billingTotals);
        }).catch(function errorCallback(error) {
            console.error('Error loading billing totals:', error);
            $scope.billingTotals = [];
        });
    }

    // Get count for a specific plan by plan name
    $scope.getPlanCount = function(planName) {
        if (!$scope.activePlanCounts || !Array.isArray($scope.activePlanCounts)) {
            return 0;
        }
        var plan = $scope.activePlanCounts.find(function(item) {
            return item && item.plan_name && item.plan_name.toLowerCase() === planName.toLowerCase();
        });
        return plan && plan.total_count ? parseInt(plan.total_count) : 0;
    }

    // Calculate total active subscriptions
    $scope.getTotalActiveSubscriptions = function() {
        var total = 0;
        if ($scope.activePlanCounts && Array.isArray($scope.activePlanCounts)) {
            $scope.activePlanCounts.forEach(function(item) {
                if (item && item.total_count) {
                    total += parseInt(item.total_count) || 0;
                }
            });
        }
        return total;
    }

    // Get active plan counts
    $scope.getActivePlanCounts = function() {
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_active_plan_counts",
        }).then(function successCallback(response) {
            $scope.activePlanCounts = response.data.active_plan_counts || [];
            console.log('Active plan counts loaded:', $scope.activePlanCounts);
        }).catch(function errorCallback(error) {
            console.error('Error loading active plan counts:', error);
            $scope.activePlanCounts = [];
        });
    }

    // Get ticket count for a specific status
    $scope.getTicketCountByStatus = function(statusName) {
        if (!$scope.ticketCountsByStatus || !Array.isArray($scope.ticketCountsByStatus)) {
            return 0;
        }
        var status = $scope.ticketCountsByStatus.find(function(item) {
            return item && item.status && item.status.toLowerCase() === statusName.toLowerCase();
        });
        return status && status.total_count ? parseInt(status.total_count) : 0;
    }

    // Calculate total ticket requests
    $scope.getTotalTicketRequests = function() {
        var total = 0;
        if ($scope.ticketCountsByStatus && Array.isArray($scope.ticketCountsByStatus)) {
            $scope.ticketCountsByStatus.forEach(function(item) {
                if (item && item.total_count) {
                    total += parseInt(item.total_count) || 0;
                }
            });
        }
        return total;
    }

    // Get ticket counts by status
    $scope.getTicketCountsByStatus = function() {
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_ticket_counts_by_status",
        }).then(function successCallback(response) {
            $scope.ticketCountsByStatus = response.data.ticket_counts || [];
            console.log('Ticket counts by status loaded:', $scope.ticketCountsByStatus);
        }).catch(function errorCallback(error) {
            console.error('Error loading ticket counts by status:', error);
            $scope.ticketCountsByStatus = [];
        });
    }

    // Initialize the controller
    $scope.init();
}]);
app.controller("ng-header-admin", ['$scope', '$http', '$document', '$interval', function ($scope, $http, $document, $interval) {

    // Initialize notification panel state
    $scope.notificationPanelOpen = false;
    $scope.pollingInterval = null;
    $scope.pollingIntervalMs = 30000; // Poll every 30 seconds (30000ms)
    $scope.hasMoreNotifications = false;

    $scope.init = function() {
        console.log('Header with notifications detected!');
        $scope.notificationPanelOpen = false;
        // Initial load with limit of 5 notifications
        $scope.getNotifications(null, 5);
        
        // Start polling for notifications
        $scope.startPolling();
        
        // Handle clicks outside notification dropdown
        $document.on('click', function(event) {
            var notificationDropdown = angular.element(document.querySelector('.notification-dropdown'));
            
            // Check if click is outside the notification dropdown
            if ($scope.notificationPanelOpen && 
                notificationDropdown.length > 0 &&
                !notificationDropdown[0].contains(event.target)) {
                $scope.$apply(function() {
                    $scope.notificationPanelOpen = false;
                });
            }
        });
    };

    // Helper function to format notification date
    $scope.formatNotificationDate = function(dateStr) {
        if (!dateStr) return '';
        
        // Remove microseconds if present (format: 2025-10-29 14:28:41.571694)
        var cleanDateStr = dateStr.split('.')[0];
        
        // Replace space with 'T' to create ISO-like format for Date constructor
        // Replace ' ' with 'T' and add 'Z' for UTC, or parse directly
        var date = new Date(cleanDateStr.replace(' ', 'T'));
        
        // Check if date is valid
        if (isNaN(date.getTime())) return '';
        
        // Format as readable string: "Oct 29, 2025 2:28 PM"
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    };

    $scope.getNotifications = function(since, limit) {
        var url = $scope.baseUrl + "api/get_notifications";
        var params = [];
        if (since) {
            params.push("since=" + encodeURIComponent(since));
        }
        if (limit) {
            params.push("limit=" + limit);
        }
        if (params.length > 0) {
            url += "?" + params.join("&");
        }
        
        $http({
            method: "GET",
            url: url
        }).then(function successCallback(response) {
            var notifications = response.data.notifications;
            var isPolling = !!since;
            var isInitialLoad = !isPolling;
            
            console.log('Notifications response - since:', since, 'count:', notifications ? notifications.length : 0);
            
            // Handle invalid response
            if (!Array.isArray(notifications)) {
                if (isInitialLoad) {
                    $scope.notifications = [];
                    $scope.notificationCount = 0;
                }
                return;
            }
            
            // Create Set of existing notification IDs for O(1) lookup
            var existingIds = new Set();
            if ($scope.notifications) {
                $scope.notifications.forEach(function(n) {
                    existingIds.add(n.id);
                });
            }
            
            // Process notifications
            var newNotifications = [];
            notifications.forEach(function(notification) {
                // Skip duplicates during polling
                if (isPolling && existingIds.has(notification.id)) {
                    return;
                }
                
                // Format date if present
                if (notification.created_at) {
                    notification.formatted_date = $scope.formatNotificationDate(notification.created_at);
                }
                
                newNotifications.push(notification);
            });
            
            // Update notifications list
            if (isPolling && newNotifications.length > 0) {
                // Prepend new notifications to existing list
                $scope.notifications = newNotifications.concat($scope.notifications || []);
            } else if (isInitialLoad) {
                // Initial load - replace all notifications
                $scope.notifications = notifications.map(function(n) {
                    if (n.created_at) {
                        n.formatted_date = $scope.formatNotificationDate(n.created_at);
                    }
                    return n;
                });
            }
            
            // Update count (only unread notifications where is_read = 'f')
            $scope.notificationCount = $scope.notifications ? $scope.notifications.filter(function(n) { return n.is_read === 'f'; }).length : 0;
            
            // Check if there are more notifications to load
            // If we got 5 or more notifications, there might be more
            if (isInitialLoad) {
                $scope.checkForMoreNotifications();
            } else if (isPolling) {
                // After polling, check again
                $scope.checkForMoreNotifications();
            }
        }).catch(function errorCallback(error) {
            console.error('Error getting notifications:', error);
            if (!since) {
                $scope.notificationCount = 0;
            }
        });
    };
    
    // Check if there are more notifications to load
    $scope.checkForMoreNotifications = function() {
        if (!$scope.notifications || $scope.notifications.length === 0) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        // Get the oldest notification's date
        var oldestNotification = $scope.notifications[$scope.notifications.length - 1];
        if (!oldestNotification || !oldestNotification.created_at) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        // Check if there are more notifications before the oldest one
        var before = $scope.cleanDateForSince(oldestNotification.created_at);
        var url = $scope.baseUrl + "api/get_notifications?before=" + encodeURIComponent(before) + "&limit=1";
        
        $http({
            method: "GET",
            url: url
        }).then(function successCallback(response) {
            var notifications = response.data.notifications;
            $scope.hasMoreNotifications = Array.isArray(notifications) && notifications.length > 0;
        }).catch(function errorCallback(error) {
            console.error('Error checking for more notifications:', error);
            $scope.hasMoreNotifications = false;
        });
    };
    
    // Load previous (older) notifications
    $scope.loadPreviousNotifications = function(event) {
        if (event) {
            event.stopPropagation();
        }
        
        if (!$scope.notifications || $scope.notifications.length === 0) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        // Get the oldest notification's date
        var oldestNotification = $scope.notifications[$scope.notifications.length - 1];
        if (!oldestNotification || !oldestNotification.created_at) {
            $scope.hasMoreNotifications = false;
            return;
        }
        
        var before = $scope.cleanDateForSince(oldestNotification.created_at);
        var url = $scope.baseUrl + "api/get_notifications?before=" + encodeURIComponent(before) + "&limit=5";
        
        $http({
            method: "GET",
            url: url
        }).then(function successCallback(response) {
            var notifications = response.data.notifications;
            
            if (!Array.isArray(notifications) || notifications.length === 0) {
                $scope.hasMoreNotifications = false;
                return;
            }
            
            // Format dates for new notifications
            notifications.forEach(function(notification) {
                if (notification.created_at) {
                    notification.formatted_date = $scope.formatNotificationDate(notification.created_at);
                }
            });
            
            // Append to existing notifications
            $scope.notifications = $scope.notifications.concat(notifications);
            
            // Check if there are more notifications to load
            $scope.checkForMoreNotifications();
        }).catch(function errorCallback(error) {
            console.error('Error loading previous notifications:', error);
            $scope.hasMoreNotifications = false;
        });
    };

    // Helper function to clean date string for 'since' parameter (remove microseconds)
    $scope.cleanDateForSince = function(dateStr) {
        return dateStr ? dateStr.split('.')[0] : null;
    };

    // Start polling for new notifications
    $scope.startPolling = function() {
        // Stop any existing polling
        $scope.stopPolling();
        
        // Start polling interval
        $scope.pollingInterval = $interval(function() {
            var since = null;
            var notifications = $scope.notifications;
            var count = notifications ? notifications.length : 0;
            
            if (count > 0) {
                // Use the most recent notification's created_at timestamp
                since = $scope.cleanDateForSince(notifications[0].created_at);
                console.log('Polling with since:', since, 'Current notifications count:', count);
            } else {
                console.log('Polling without since (full refresh), current notifications count:', count);
            }
            
            $scope.getNotifications(since);
        }, $scope.pollingIntervalMs);
        
        console.log('Notification polling started (interval: ' + $scope.pollingIntervalMs + 'ms)');
    };

    // Stop polling for notifications
    $scope.stopPolling = function() {
        if ($scope.pollingInterval) {
            $interval.cancel($scope.pollingInterval);
            $scope.pollingInterval = null;
            console.log('Notification polling stopped');
        }
    };

    // Toggle notification panel
    $scope.toggleNotificationPanel = function(event) {
        if (event) {
            event.stopPropagation();
        }
        $scope.notificationPanelOpen = !$scope.notificationPanelOpen;
    };

    $scope.markNotificationAsRead = function(notificationId, event) {
        event.stopPropagation();
        // console.log('Mark notification as read:', notificationId);
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/mark_as_read",
            data: {
                id: notificationId
            }
        }).then(function successCallback(response) {
            // console.log('Notification marked as read:', response.data);
            // Refresh notifications to update the list and count (keep same limit as initial load)
            $scope.getNotifications(null, 5);
        }).catch(function errorCallback(error) {
            console.error('Error marking notification as read:', error);
        });
    };

    $scope.markAllAsRead = function(event) {
        event.stopPropagation();
        //console.log('Mark all notifications as read');
        $http({
            method: "POST",
            url: $scope.baseUrl + "api/mark_all_as_read",
            data: {
            }
        }).then(function successCallback(response) {
            // Refresh notifications to update the list and count (keep same limit as initial load)
            $scope.getNotifications(null, 5);
        }).catch(function errorCallback(error) {
            console.error('Error marking all notifications as read:', error);
        });
    };

    // Handle notification click - navigate based on notification type
    $scope.handleNotificationClick = function(notification, event) {
        if (event) {
            event.stopPropagation();
        }
        
        // Mark notification as read
        $scope.markNotificationAsRead(notification.id, event);
        
        // Check if notification is subscription-related
        if (notification.type === 'subscription' || !notification.ticket_id) {
            // Navigate to customers module
            $scope.navigateToCustomers();
        } else {
            // Open ticket modal - try to access parent scope's openModal function
            // Check if we're in dashboard context and can access parent scope
            var dashboardScope = $scope.$parent;
            while (dashboardScope && !dashboardScope.openModal) {
                dashboardScope = dashboardScope.$parent;
            }
            
            if (dashboardScope && dashboardScope.openModal) {
                dashboardScope.openModal('modal_view_request', notification.ticket_id);
            } else {
                // Fallback: try to find openModal in root scope or use window
                if (window.angular && window.angular.element(document.querySelector('[ng-controller="ng-dashboard-admin"]')).scope()) {
                    var dashboardController = window.angular.element(document.querySelector('[ng-controller="ng-dashboard-admin"]')).scope();
                    if (dashboardController && dashboardController.openModal) {
                        dashboardController.openModal('modal_view_request', notification.ticket_id);
                    }
                }
            }
        }
    };

    // Navigate to customers module
    $scope.navigateToCustomers = function() {
        // Try to access parent scope's openModule function
        var dashboardScope = $scope.$parent;
        while (dashboardScope && !dashboardScope.openModule) {
            dashboardScope = dashboardScope.$parent;
        }
        
        if (dashboardScope && dashboardScope.openModule) {
            dashboardScope.openModule('customers');
        } else {
            // Fallback: try to find openModule in root scope or use window
            if (window.angular && window.angular.element(document.querySelector('[ng-controller="ng-dashboard-admin"]')).scope()) {
                var dashboardController = window.angular.element(document.querySelector('[ng-controller="ng-dashboard-admin"]')).scope();
                if (dashboardController && dashboardController.openModule) {
                    dashboardController.openModule('customers');
                }
            }
        }
    };

    // Clean up polling when controller is destroyed
    $scope.$on('$destroy', function() {
        $scope.stopPolling();
    });

    $scope.openUserProfileModal = function(modalId) {
        $('#' + modalId).removeClass('hidden');
        $('#' + modalId).addClass('flex');

        console.log("Modal ID:", modalId);
        
        // Disable page scroll when modal is open
        $('body').addClass('modal-open');
    };



    $scope.init();

}]);
app.controller("ng-customer", ['$scope', '$http', function ($scope, $http) {
    $scope.loadingCustomers = true;
    $scope.customers = [];
    $scope.filteredData = [];
    
    // Search
    $scope.searchText = '';
    
    // Pagination
    $scope.currentPage = 1;
    $scope.itemsPerPage = 5;
    
    // Watch for search text changes and reset pagination
    $scope.$watch('searchText', function(newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.currentPage = 1;
            $scope.applyFilter();
        }
    });

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

        $scope.getAllCustomers();
    };

    // Filter function to apply search
    $scope.applyFilter = function() {
        if ($scope.searchText && $scope.searchText.trim() !== '') {
            $scope.filteredData = $scope.customers.filter(function(item) {
                return (
                    (item.name && item.name.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.email && item.email.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.plan_name && item.plan_name.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.hours_remaining && item.hours_remaining.toString().includes($scope.searchText)) ||
                    (item.status && item.status.toLowerCase().includes($scope.searchText.toLowerCase()))
                );
            });
        } else {
            $scope.filteredData = $scope.customers;
        }
    };

    $scope.getAllCustomers = function() {
        $scope.loadingCustomers = true;
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_all_customers",
        }).then(function successCallback(response) {
            $scope.customers = response.data.customers;
            $scope.applyFilter();
        }).finally(function() {
            $scope.loadingCustomers = false;
        });
    };

    // Calculate total pages
    $scope.customerPageCount = function() {
        if (!$scope.customers) return 0;
        // Use filteredData if search is active, otherwise use customers
        const dataToUse = $scope.searchText && $scope.searchText.trim() !== '' ? $scope.filteredData : $scope.customers;
        if (!dataToUse) return 0;
        return Math.ceil(dataToUse.length / $scope.itemsPerPage);
    };

    // Go to page
    $scope.customerSetPage = function(page) {
        $scope.currentPage = page;
    };

    // Previous page
    $scope.customerPrevPage = function() {
        if ($scope.currentPage > 1) {
          $scope.currentPage--;
        }
    };

    // Next page
    $scope.customerNextPage = function() {
        if ($scope.currentPage < $scope.customerPageCount()) {
          $scope.currentPage++;
        }
    };

    // Get pages for pagination display with ellipsis
    $scope.customerGetPages = function() {
        let pages = [];
        const totalPages = $scope.customerPageCount();
        const currentPage = $scope.currentPage;
        
        if (totalPages <= 4) {
            // Show all pages if 4 or fewer
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            let startPage, endPage;
            
            // Near the end: show last 3 pages
            if (currentPage >= totalPages - 1) {
                startPage = Math.max(1, totalPages - 2);
                endPage = totalPages;
            }
            // Near the beginning: show first 3 pages
            else if (currentPage <= 2) {
                startPage = 1;
                endPage = 3;
            }
            // Pages 3-4: center around current (current-1, current, current+1)
            else if (currentPage <= 4) {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            // Page 5+: show current-1, current, current+1
            else {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            
            // Add the 3 pages
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                pages.push('...');
                pages.push(totalPages);
            }
        }
        
        return pages;
    };



}]);
app.controller("ng-billing", ['$scope', '$http', function ($scope, $http) {
    $scope.loadingBilling = true;
    $scope.billing = [];
    $scope.filteredData = [];
    
    // Search
    $scope.searchText = '';
    
    // Pagination
    $scope.currentPage = 1;
    $scope.itemsPerPage = 5;
    
    // Watch for search text changes and reset pagination
    $scope.$watch('searchText', function(newVal, oldVal) {
        if (newVal !== oldVal) {
            $scope.currentPage = 1;
            $scope.applyFilter();
        }
    });

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

        $scope.getAllBilling();
    };

    // Filter function to apply search
    $scope.applyFilter = function() {
        if ($scope.searchText && $scope.searchText.trim() !== '') {
            $scope.filteredData = $scope.billing.filter(function(item) {
                return (
                    (item.name && item.name.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.email && item.email.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.billing_type && item.billing_type.toLowerCase().includes($scope.searchText.toLowerCase())) ||
                    (item.amount && item.amount.toString().includes($scope.searchText)) ||
                    (item.status && item.status.toLowerCase().includes($scope.searchText.toLowerCase()))
                );
            });
        } else {
            $scope.filteredData = $scope.billing;
        }
    };

    $scope.getAllBilling = function() {
        $scope.loadingBilling = true;
        $http({
            method: "GET",
            url: $scope.baseUrl + "api/get_all_billing",
        }).then(function successCallback(response) {
            $scope.billing = response.data.billing;
            $scope.applyFilter();
        }).finally(function() {
            $scope.loadingBilling = false;
        });
    };

    // Calculate total pages
    $scope.billingPageCount = function() {
        if (!$scope.billing) return 0;
        // Use filteredData if search is active, otherwise use billing
        const dataToUse = $scope.searchText && $scope.searchText.trim() !== '' ? $scope.filteredData : $scope.billing;
        if (!dataToUse) return 0;
        return Math.ceil(dataToUse.length / $scope.itemsPerPage);
    };

    // Go to page
    $scope.billingSetPage = function(page) {
        $scope.currentPage = page;
    };

    // Previous page
    $scope.billingPrevPage = function() {
        if ($scope.currentPage > 1) {
          $scope.currentPage--;
        }
    };

    // Next page
    $scope.billingNextPage = function() {
        if ($scope.currentPage < $scope.billingPageCount()) {
          $scope.currentPage++;
        }
    };

    // Get pages for pagination display with ellipsis
    $scope.billingGetPages = function() {
        let pages = [];
        const totalPages = $scope.billingPageCount();
        const currentPage = $scope.currentPage;
        
        if (totalPages <= 4) {
            // Show all pages if 4 or fewer
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            let startPage, endPage;
            
            // Near the end: show last 3 pages
            if (currentPage >= totalPages - 1) {
                startPage = Math.max(1, totalPages - 2);
                endPage = totalPages;
            }
            // Near the beginning: show first 3 pages
            else if (currentPage <= 2) {
                startPage = 1;
                endPage = 3;
            }
            // Pages 3-4: center around current (current-1, current, current+1)
            else if (currentPage <= 4) {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            // Page 5+: show current-2, current-1, current
            else {
                startPage = currentPage - 1;
                endPage = currentPage + 1;
            }
            
            // Add the 3 pages
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            
            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                pages.push('...');
                pages.push(totalPages);
            }
        }
        
        return pages;
    };

    // Download invoice PDF from Stripe
    $scope.downloadInvoicePDF = function(invoiceId) {
        if (!invoiceId) {
            alert('Invoice ID is missing');
            return;
        }

        // Create download URL
        const downloadUrl = $scope.baseUrl + 'api/download_invoice_pdf?invoice_id=' + encodeURIComponent(invoiceId);
        
        // Open in new window to trigger download
        window.open(downloadUrl, '_blank');
    };



}]);
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
// Controller Script For Global Variables
// This controller manages global variables and state for the application

app.controller("ng-test-variables", ['$scope', function($scope) {
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

    // Initialize the controller
    $scope.init();
}]); 