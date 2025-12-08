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