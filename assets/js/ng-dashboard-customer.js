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
        const hasCi3Template = currentPath.includes('/ci3_template/');
          
        // Use ci3_template path if we're local OR if the current URL contains ci3_template
        const baseUrl = (isLocal || hasCi3Template) ? '/ci3_template/' : '/';
        

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