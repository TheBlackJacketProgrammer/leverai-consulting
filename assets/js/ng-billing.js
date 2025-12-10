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