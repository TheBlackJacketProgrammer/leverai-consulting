<!-- Request History -->
<div class="admin-module-requests" ng-init="getAllRequests()">
    <section class="full-bleed">
        <div class="section-requests">
            <div class="black-container">
                <div class="flex flex-row items-center justify-between w-full">
                    <h5>Request History</h5>
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
                    <div ng-if="loadingTickets" class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="text-white mt-2">Loading tickets...</p>
                    </div>
                                
                    <!-- Table - Desktop -->
                    <table class="table-auto text-white" ng-if="!loadingTickets">
                        <thead class="text-left">
                            <tr>
                                <th class="text-left">Ticket ID</th>
                                <th class="text-left">Request</th>
                                <!-- <th class="text-left">Hours</th> -->
                                <th class="text-left">Status</th>
                                <th class="text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="ticket in filteredData | limitTo: itemsPerPage : (currentPage-1)*itemsPerPage">
                                <td>{{ ticket.ticket_id }}</td>
                                <td>{{ ticket.title }}</td>
                                <!-- <td>{{ ticket.dedicate_hours != '0' ? (ticket.dedicate_hours + ' hrs') : '-' }}</td> -->
                                <td>
                                    <span ng-class="{
                                                    'status-pending': ticket.status.toLowerCase() === 'pending',
                                                    'status-reject': ticket.status.toLowerCase() === 'rejected',
                                                    'status-implement': ticket.status.toLowerCase() === 'implemented'
                                    }">{{ ticket.status }}</span>
                                </td>
                                <td>
                                    <button class="btn-secondary" ng-click="openModal('modal_view_request', ticket.id)">View Request</button>
                                </td>
                            </tr>
                            <tr ng-if="filteredData.length === 0">
                                <td colspan="5" style="text-align:center; padding: 10px;">No Requests Found.</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Table - Mobile -->
                    <div class="list-tickets" ng-if="!loadingTickets">
                        <div class="ticket-item " ng-repeat="ticket in filteredData | limitTo: itemsPerPage : (currentPage-1)*itemsPerPage">
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Ticket ID</p>
                                    <p class="primary">{{ ticket.ticket_id }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row w-full">
                                    <p class="mr-2">Request</p>
                                    <p class="primary">{{ ticket.title }}</p>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row items-center justify-between w-full">
                                    <!-- <div class="flex flex-row justify-start w-full">
                                        <p class="mr-2">Dedicated Hrs</p>
                                        <p class="primary">{{ ticket.dedicate_hours || '-' }}</p>
                                    </div> -->
                                    <!-- <div class="flex flex-row justify-end w-full"> -->
                                    <div class="flex flex-row justify-start w-full">
                                        <p class="mr-2">Status</p>
                                        <p ng-class="{
                                            'status-pending': ticket.status.toLowerCase() === 'pending',
                                            'status-reject': ticket.status.toLowerCase() === 'reject',
                                            'status-implement': ticket.status.toLowerCase() === 'implement',
                                            'primary': ticket.status.toLowerCase() !== 'pending' && ticket.status.toLowerCase() !== 'reject' && ticket.status.toLowerCase() !== 'implement'
                                        }">{{ ticket.status }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row-span-1">
                                <div class="flex flex-row justify-end w-full">
                                    <button class="btn-secondary my-1" ng-click="openModal('modal_view_request', ticket.id)">View Request</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination" ng-if="!loadingTickets && ((searchText && searchText.trim() !== '' && filteredData && filteredData.length > 0) || (!searchText || searchText.trim() === '') && tickets && tickets.length > 0)">
                        <button ng-disabled="currentPage == 1" ng-click="prevPage()">Prev</button>
                        <span ng-repeat="page in getPages() track by (page === '...' ? 'ellipsis-' + $index : page)">
                            <button ng-if="page !== '...'"
                                    ng-click="setPage(page)" 
                                    ng-class="{active: currentPage === page}">
                            {{page}}
                            </button>
                            <span ng-if="page === '...'" class="pagination-ellipsis">...</span>
                        </span>
                        <button ng-disabled="currentPage == pageCount()" ng-click="nextPage()">Next</button>
                    </div>
                </div>
            </div>
        </div>             
    </section>
</div>
