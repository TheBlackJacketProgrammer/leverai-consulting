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
        const hasCi3Template = currentPath.includes('/ci3_template/');
          
        // Use ci3_template path if we're local OR if the current URL contains ci3_template
        const baseUrl = (isLocal || hasCi3Template) ? '/ci3_template/' : '/';
        

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