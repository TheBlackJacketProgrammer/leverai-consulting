<!-- Customer History -->
<div class="admin-module-billing" ng-controller="ng-billing" ng-init="getAllBilling()">
    <section class="full-bleed">
        <div class="section-requests">
            <div class="black-container">
                <div class="flex flex-row items-center justify-between w-full">
                    <h5>Billing History</h5>
                </div>
                <div class="divider my-2"></div>
                <div class="flex flex-col items-end justify-center w-full">
                    <div class="search-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_6_16189)">
                            <path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" fill="white"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_6_16189">
                                    <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <input type="text" id="search" ng-model="searchText" placeholder="Search">
                    </div>
                </div>
                <div class="divider my-2"></div>
                <div class="flex flex-col items-center justify-center w-full">
                    <!-- Loading Spinner -->
                    <div ng-if="loadingBilling" class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="text-white mt-2">Loading data...</p>
                    </div>
                                
                    <!-- Table - Desktop -->
                    <table class="table-auto text-white" ng-if="!loadingBilling">
                        <thead class="text-left">
                            <tr>
                                <!-- <th class="text-left">Invoice Id</th> -->
                                <th class="text-left">Fullname</th>
                                <th class="text-left">Email</th>
                                <th class="text-left">Billing Type</th>
                                <th class="text-left">Amount</th>
                                <th class="text-left">Status</th>
                                <th class="text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="client in filteredData | limitTo: itemsPerPage : (currentPage-1)*itemsPerPage">
                                <!-- <td>{{ client.invoice_number }}</td> -->
                                <td>{{ client.name }}</td>
                                <td>{{ client.email }}</td>
                                <td>{{ client.billing_type }}</td>
                                <td>{{ client.amount }}</td>
                                <td>{{ client.status }}</td>
                                <td>
                                    <button ng-if="client.stripe_invoice_id && client.status.toLowerCase() === 'paid'" 
                                            class="btn-secondary" 
                                            ng-click="downloadInvoicePDF(client.stripe_invoice_id)"
                                            style="padding: 5px 10px; font-size: 12px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" fill="currentColor"/>
                                        </svg>
                                        Download PDF
                                    </button>
                                    <p ng-if="client.billing_type.toLowerCase() === 'topup'">No Invoice Available</p>
                                    <!-- <span ng-if="!client.stripe_invoice_id || client.status.toLowerCase() !== 'paid'" style="color: #888; font-size: 12px;">-</span> -->
                                </td>
                            </tr>
                            <tr ng-if="filteredData.length === 0">
                                <td colspan="6" style="text-align:center; padding: 10px;">No Requests Found.</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Table - Mobile -->
                    <div class="list-tickets" ng-if="!loadingBilling">
                        <div class="ticket-item " ng-repeat="client in filteredData | limitTo: itemsPerPage : (currentPage-1)*itemsPerPage">
                            <div class="row-span-1" hidden>
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Invoice Id</p>
                                    <p class="primary">{{ client.invoice_id }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Fullname</p>
                                    <p class="primary">{{ client.name }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row items-center justify-between w-full">
                                    <div class="flex flex-row justify-start w-full">
                                        <p class="mr-2">Email</p>
                                        <p class="primary">{{ client.email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Billing Type</p>
                                    <p ng-class="{
                                            'status-success': client.billing_type.toLowerCase() === 'subscription',
                                            'top-up': client.billing_type.toLowerCase() === 'topup',
                                        }">{{ client.billing_type.toLowerCase() === 'subscription' ? 'Subscription' : (client.billing_type.toLowerCase() === 'topup' ? 'Top-Up' : client.billing_type) }}
                                    </p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row items-center justify-between w-full mb-4">
                                    <div class="flex flex-row justify-start w-2/3">
                                        <p class="mr-2">Amount</p>
                                        <p class="primary">USD ${{ client.amount }}</p>
                                    </div>
                                    <div class="flex flex-row justify-end w-1/2">
                                        <p class="mr-2">Status</p>
                                        <p ng-class="{
                                            'status-pending': client.status.toLowerCase() === 'pending',
                                            'status-success': client.status.toLowerCase() === 'paid',
                                            'status-reject': client.status.toLowerCase() === 'rejected',
                                        }">{{ client.status.toLowerCase() === 'pending' ? 'Pending' : (client.status.toLowerCase() === 'paid' ? 'Paid' : 'Rejected') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row-span-1" ng-if="client.stripe_invoice_id && client.status.toLowerCase() === 'paid'">
                                <div class="flex flex-row justify-end w-full">
                                    <button class="btn-secondary my-1" ng-click="downloadInvoicePDF(client.stripe_invoice_id)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" fill="currentColor"/>
                                        </svg>
                                        Download Invoice PDF
                                    </button>
                                </div>
                            </div>
                            <div class="row-span-1" ng-if="client.billing_type.toLowerCase() === 'topup'">
                               <p>No Invoice Available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination" ng-if="!loadingBilling && ((searchText && searchText.trim() !== '' && filteredData && filteredData.length > 0) || (!searchText || searchText.trim() === '') && billing && billing.length > 0)">
                        <button ng-disabled="currentPage == 1" ng-click="billingPrevPage()">Prev</button>
                        <span ng-repeat="page in billingGetPages() track by (page === '...' ? 'ellipsis-' + $index : page)">
                            <button ng-if="page !== '...'"
                                    ng-click="billingSetPage(page)" 
                                    ng-class="{active: currentPage === page}">
                            {{page}}
                            </button>
                            <span ng-if="page === '...'" class="pagination-ellipsis">...</span>
                        </span>
                        <button ng-disabled="currentPage == billingPageCount()" ng-click="billingNextPage()">Next</button>
                    </div>
                </div>
            </div>
        </div>             
    </section>
</div>