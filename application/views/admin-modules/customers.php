<!-- Customer History -->
<div class="admin-module-customers" ng-controller="ng-customer" ng-init="getAllCustomers()">
    <section class="full-bleed">
        <div class="section-requests">
            <div class="black-container">
                <div class="flex flex-row items-center justify-between w-full">
                    <h5>Client Masterlist</h5>
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
                    <div ng-if="loadingCustomers" class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="text-white mt-2">Loading clients...</p>
                    </div>
                                
                    <!-- Table - Desktop -->
                    <table class="table-auto text-white" ng-if="!loadingCustomers">
                        <thead class="text-left">
                            <tr>
                                <th class="text-left" hidden>Customer Id</th>
                                <th class="text-left">Fullname</th>
                                <th class="text-left">Email</th>
                                <th class="text-left">Plan</th>
                                <th class="text-left">Remaining Hours</th>
                                <th class="text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="client in filteredData | limitTo: itemsPerPage : (currentPage-1)*itemsPerPage">
                                <td hidden>{{ client.user_id }}</td>
                                <td>{{ client.name }}</td>
                                <td>{{ client.email }}</td>
                                <td>{{ client.plan_name }}</td>
                                <td>{{ client.hours_remaining }}</td>
                                <td ng-class="{
                                    'status-implement': client.status.toLowerCase() === 'active',
                                    'status-reject': client.status.toLowerCase() === 'deactivate' || client.status.toLowerCase() === 'deactivated',
                                    'primary': client.status.toLowerCase() !== 'active' && client.status.toLowerCase() !== 'deactivate' && client.status.toLowerCase() !== 'deactivated'
                                }">{{ client.status.toLowerCase() === 'active' ? 'Active' : (client.status.toLowerCase() === 'deactivate' || client.status.toLowerCase() === 'deactivated' ? 'Deactivated' : client.status) }}</td>
                            </tr>
                            <tr ng-if="filteredData.length === 0">
                                <td colspan="5" style="text-align:center; padding: 10px;">No Requests Found.</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Table - Mobile -->
                    <div class="list-tickets" ng-if="!loadingCustomers">
                        <div class="ticket-item " ng-repeat="client in filteredData | limitTo: itemsPerPage : (currentPage-1)*itemsPerPage">
                            <div class="row-span-1" hidden>
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Customer Id</p>
                                    <p class="primary">{{ client.user_id }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Fullname</p>
                                    <p class="primary">{{ client.name }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Email</p>
                                    <p class="primary">{{ client.email }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Subscription Plan</p>
                                    <p class="primary">{{ client.plan_name === 'pro' ? 'Pro' : (client.plan_name === 'standard' ? 'Standard' : 'Basic') }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row items-center justify-between w-full mb-4">
                                    <div class="flex flex-row justify-start w-1/2">
                                        <p class="mr-2">Hours Left</p>
                                        <p class="primary">{{ client.hours_remaining }}</p>
                                    </div>
                                    <div class="flex flex-row justify-end w-1/2">
                                        <p class="mr-2">Status</p>
                                        <p ng-class="{
                                            'status-implement': client.status.toLowerCase() === 'active',
                                            'status-reject': client.status.toLowerCase() === 'deactivate' || client.status.toLowerCase() === 'deactivated',
                                            'primary': client.status.toLowerCase() !== 'active' && client.status.toLowerCase() !== 'deactivate' && client.status.toLowerCase() !== 'deactivated'
                                        }">{{ client.status.toLowerCase() === 'active' ? 'Active' : (client.status.toLowerCase() === 'deactivate' || client.status.toLowerCase() === 'deactivated' ? 'Deactivated' : client.status) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row-span-1" hidden>
                                <div class="flex flex-row justify-end w-full">
                                    <button class="btn-secondary my-1" ng-click="openModal('modal_view_request', ticket.id)">View Request</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination" ng-if="!loadingCustomers && ((searchText && searchText.trim() !== '' && filteredData && filteredData.length > 0) || (!searchText || searchText.trim() === '') && customers && customers.length > 0)">
                        <button ng-disabled="currentPage == 1" ng-click="customerPrevPage()">Prev</button>
                        <span ng-repeat="page in customerGetPages() track by (page === '...' ? 'ellipsis-' + $index : page)">
                            <button ng-if="page !== '...'"
                                    ng-click="customerSetPage(page)" 
                                    ng-class="{active: currentPage === page}">
                            {{page}}
                            </button>
                            <span ng-if="page === '...'" class="pagination-ellipsis">...</span>
                        </span>
                        <button ng-disabled="currentPage == customerPageCount()" ng-click="customerNextPage()">Next</button>
                    </div>
                </div>
            </div>
        </div>             
    </section>
</div>