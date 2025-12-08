<div id="modal_view_request" class="modal hidden modal_view_request">
    <!-- Modal Dialog Box -->
    <div class="modal-dialog-box">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="text-white">Ticket Details</h4>
            <button id="closeModal" ng-click="closeModal('modal_view_request')" class="btn-close primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_17_4653)">
                        <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="#0289B3"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_17_4653">
                            <rect width="24" height="24" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </button>
        </div>
        <div class="divider"></div>
        <!-- Body-->
        <div class="modal-body">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full" <?php echo ($this->session->userdata("role") == "admin") ? "" : "style='display: none;'"?>>
                <div class="ticket-details container col-span-2">
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="font-medium text-white">Ticket #</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.ticket_id }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Customer Name</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.name }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Request</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.title }}</span>
                        </p>
                    </div>
                </div>
                <div class="ticket-details container col-span-1">
                    <div class="text-sm space-y-2 w-full">
                        <p>
                            <span class="font-medium text-white">Dedicated Hrs</span>
                            <span class="primary font-semibold ml-2">{{ ticketDetails.dedicate_hours }} hrs</span>
                        </p>
                        <div>
                            <label for="status" class="font-medium text-white block mb-1">Status</label>
                            <select id="status" ng-model="ticketDetails.status" ng-change="updateStatus(ticketDetails)">
                                <option value="Ongoing" ng-selected="ticketDetails.status == 'Ongoing'">Ongoing</option>
                                <option value="Implemented" ng-selected="ticketDetails.status == 'Implemented'">Implemented</option>
                                <option value="Rejected" ng-selected="ticketDetails.status == 'Rejected'">Rejected</option>
                                <option value="Pending" ng-selected="ticketDetails.status == 'Pending'">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full" <?php echo ($this->session->userdata("role") == "customer") ? "" : "style='display: none;'"?>>
                <div class="ticket-details container col-span-2" ng-if="ticketDetails.status != 'Rejected'">
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="font-medium text-white">Ticket #</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.ticket_id }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Request</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.title }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Status</span>
                            <span class="primary font-semibold ml-2">{{ ticketDetails.status }}</span>
                        </p>
                    </div>
                </div>
                <div class="ticket-details container col-span-3" ng-if="ticketDetails.status == 'Rejected'">
                    <div class="text-sm space-y-1">
                        <p>
                            <span class="font-medium text-white">Ticket #</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.ticket_id }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Request</span>
                            <span class="primary font-semibold hover:underline ml-1">{{ ticketDetails.title }}</span>
                        </p>
                        <p>
                            <span class="font-medium text-white">Status</span>
                            <span class="primary font-semibold ml-2">{{ ticketDetails.status }}</span>
                        </p>
                    </div>
                </div>
                <div class="ticket-details container col-span-1" ng-if="ticketDetails.status != 'Rejected'">
                    <div class="text-sm space-y-2 w-full">
                        <p ng-if="ticketDetails.status != 'Ongoing'">
                            <span class="font-medium text-white">Remaining Hrs</span>
                            <span class="primary font-semibold ml-2">{{ hours_remaining }} hrs</span>
                        </p>
                        <div class="flex flex-row items-end justify-start gap-2 w-full">
                            <div class=" w-full" ng-if="ticketDetails.status == 'Ongoing'">
                                <p>
                                    <span class="font-medium text-white">Dedicated Hrs</span>
                                    <span class="primary font-semibold ml-2">{{ ticketDetails.dedicate_hours }} hrs</span>
                                </p>
                            </div>
                            <div class=" w-1/2" ng-if="ticketDetails.status != 'Ongoing'">
                                <p class="font-medium text-white my-2" ng-if="ticketDetails.status != 'Ongoing'">Dedicated Hrs</p>
                                <button class="btn-hour" ng-click="btnDedicateHours('add')" ng-if="ticketDetails.status != 'Ongoing'">+</button>
                                <span class="primary font-semibold mx-2" ng-if="ticketDetails.status != 'Ongoing'">{{ ticketDetails.dedicate_hours }} hrs</span>
                                <button class="btn-hour" ng-click="btnDedicateHours('subtract')" ng-if="ticketDetails.status != 'Ongoing'">-</button>    
                            </div>
                            <div class="flex flex-row items-end justify-center w-1/2">
                                <button class="btn-update" ng-click="btnUpdateDedicateHours()" ng-if="ticketDetails.status != 'Ongoing'">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2 w-full px-4">
                <p class="text-sm">
                    <span class=" text-white font-semibold">Request Description</span>
                </p>
                <div class="divider"></div>
            </div>
            <div class="ticket-description container">
                <textarea readonly class="text-8 font-sm font-light w-full resize-none" rows="4">{{ ticketDetails.details }}</textarea>
            </div>
            <div class="ticket-comments container mt-2">
                <p class="text-sm space-y-1">
                    <span class=" text-white font-semibolds">Comments</span>
                </p>
                <div class="divider mb-2"></div>
                <div class="comments-container" ng-class="{'scrollable': ticketComments.length >= 3}">
                    <div class="flex flex-row gap-2" ng-repeat="comment in ticketComments">
                        <div class="flex flex-col gap-1 items-start justify-start w-10">
                            <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center">
                                <p class="primary text-sm font-semibold">{{ comment.usertype == 'admin' ? 'LAI' : (comment.user_name ? comment.user_name.charAt(0).toUpperCase() : 'C') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 w-full">
                            <h5 class="primary font-semibold">{{ comment.usertype == 'admin' ? 'Lever A.I Dev Team' : (comment.user_name || 'Customer') }}</h5>
                            <p class="text-sm text-8 text-white">{{ comment.message }}</p>
                            <p class="dt-tag">{{ comment.created_at | date:'dd/MM/yyyy HH:mm' }}</p>
                            <p class="dt-error" ng-if="comment.error">Error in sending comment</p>
                        </div>
                    </div>
                    <div ng-if="ticketComments.length === 0" class="text-center py-4">
                        <p class="text-gray-400 text-sm">No Comments Yet</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-2 w-full" ng-if="ticketDetails.status == 'Rejected'">
                <p class="text-sm text-white">Ticket has been rejected and closed.</p>
            </div>
            <div class="flex items-center gap-2 w-full" ng-if="ticketDetails.status != 'Rejected'">
                <!-- Upload Icon -->
                <button class="p-2 btn-secondary-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 0l-4 4m4-4l4 4" />
                    </svg>
                </button>

                <!-- Input Field -->
                <textarea ng-model="commentData.text" ng-disabled="sendingComment"
                    placeholder="Type your comment here..."
                    rows="1">
                </textarea>

                <!-- Send Button -->
                <button class="btn-primary" ng-click="sendComment(ticketDetails)" ng-disabled="sendingComment">
                    <span ng-if="!sendingComment">Send</span>
                    <span ng-if="sendingComment">Sending...</span>
                </button>
            </div>
        </div>
    </div>
</div>