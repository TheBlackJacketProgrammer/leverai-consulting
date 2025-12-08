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
        const hasCi3Template = currentPath.includes('/ci3_template/');
          
        // Use ci3_template path if we're local OR if the current URL contains ci3_template
        const baseUrl = (isLocal || hasCi3Template) ? '/ci3_template/' : '/';

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